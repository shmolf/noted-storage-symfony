<?php

namespace App\Controller\User;

use App\Dto\AppTokenDto;
use App\Entity\AppToken;
use App\Entity\User;
use App\Exception\AppTokenException;
use App\Exception\OAuthException;
use App\Exception\UserCreationException;
use App\Exception\UserModificationException;
use App\Utility\QoL;
use App\Utility\Random;
use App\Utility\UserInputStrings;
use DateTime;
use Exception;
use Psalm\Report\JsonReport;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @method User|null getUser()
 */
class AccountController extends AbstractController
{
    private const CRSF_GENERATE_APP_TKN = 'generate-token';

    private UserPasswordEncoderInterface $passwordEncoder;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function edit(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('account/edit.html.twig', ['user' => $this->getUser()]);
    }

    public function saveOld(Request $request): Response
    {
        $requestData = $request->request->all();
        $email = UserInputStrings::trimMb4String($requestData['email'] ?? '');
        $password = isset($requestData['password']) ? $requestData['password'] : null;
        $errors = [];

        if (mb_strlen($email) === 0 || preg_match(UserInputStrings::REGEX_EMAIL, $email) !== 1) {
            $errors[] = 'Email is invalid';
        } elseif (mb_strlen($password ?? '') === 0 || preg_match(UserInputStrings::REGEX_PASSWORD, $password) !== 1) {
            $errors[] = 'Password is invalid';
        }

        /** @var User|null */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ 'email' => $email ]);
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            $errors[] = 'User is not logged in';
        } elseif ($user !== null && $user->getEmail() !== $authenticatedUser->email) {
            $errors[] = 'User already exists';
        }

        if (count($errors) > 0) {
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors($errors);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $authenticatedUser->setEmail($email);
        $authenticatedUser->setPassword($this->passwordEncoder->encodePassword($authenticatedUser, $password));

        try {
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$email}";
            throw (new UserCreationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs', $e))
                ->setErrors([$error]);
        }

        return $this->redirectToRoute('login');
    }

    public function save(Request $request): Response
    {
        $input = $this->parseInputs($request);

        $token = new CsrfToken('user-edit', $input['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        if ($input['email'] === null || preg_match(UserInputStrings::REGEX_EMAIL, $input['email']) !== 1) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Email is invalid']);
        } elseif ($input['password'] !== null && preg_match(UserInputStrings::REGEX_PASSWORD, $input['password']) !== 1
        ) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with provided inputs'))
                ->setErrors(['Password is invalid.', UserInputStrings::PASSWORD_DESCRIPTION]);
        }

        /** @var User|null */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([ 'email' => $input['email'] ]);
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error modifying the user'))
                ->setErrors(['User is not logged in']);
        } elseif ($user instanceof User && $user->getEmail() !== $authenticatedUser->getEmail()) {
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error modifying the user'))
                ->setErrors(['Error with provided email']);
        }

        $authenticatedUser->setEmail($input['email']);

        if ($input['password']) {
            $authenticatedUser->setPassword($this->passwordEncoder->encodePassword($authenticatedUser, $input['password']));
        }

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$input['email']}";
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error with saving the user'))
                ->setErrors([$error]);
        }

        return new Response();
    }

    public function appTokenList(): Response
    {
        $appTokens = array_reduce(
            $this->getUser()->getAppTokens()->toArray(),
            fn(array $tokens, AppToken $token) => QoL::arrPush($tokens, new AppTokenDto($token)),
            /** @var AppTokenDto[] */
            []
        );

        return $this->render('account/app-tokens.html.twig', [
            'tokens' => $appTokens,
            'crsfId' => self::CRSF_GENERATE_APP_TKN,
        ]);
    }

    public function createAppToken(Request $request): Response
    {
        $crsfToken = $request->request->get('_csrf_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CRSF_GENERATE_APP_TKN, $crsfToken))) {
            throw new InvalidCsrfTokenException();
        }

        // We want to remove the token from the session, so the user can't just refresh the page to create new tokens.
        $this->csrfTokenManager->removeToken(self::CRSF_GENERATE_APP_TKN);

        $tokenName = UserInputStrings::trimMb4String($request->request->get('name') ?? '');
        $tokenExpiration = $request->request->get('expiration');

        if (mb_strlen($tokenName) === 0) {
            return $this->redirectToRoute('account.oauth.app.list', [], Response::HTTP_TEMPORARY_REDIRECT);
        }

        $now = new DateTime();
        $tokenEntity = new AppToken();
        $tokenEntity
            ->setName($tokenName)
            ->setExpirationDate(new DateTime($tokenExpiration))
            ->setCreatedDate($now)
            ->setUser($this->getUser())
            ->setUuid(Uuid::uuid4()->toString())
            ->setAuthorizationToken(Random::createString(42, [Random::ALPHA_NUM]));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tokenEntity);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            throw (new AppTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the token', $e->getMessage()]);
        }

        return $this->render('account/new-app-token.html.twig', [
            'name' => $tokenEntity->getName(),
            'token' => $tokenEntity->getAuthorizationToken(),
        ]);
    }

    public function deleteAppToken(string $uuid): JsonResponse
    {
        /** @var AppToken|false */
        $token = $this->getUser()->getAppTokens()->filter(fn(AppToken $token) => $token->getUuid() === $uuid)->first();

        if ($token instanceof AppToken) {
            $user = $token->getUser();
            $user->removeAppToken($token);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->clear();

            return new JsonResponse();
        }

        return new JsonResponse([], Response::HTTP_NOT_FOUND);
    }

    public function generateOAuth(): Response
    {
        $authenticatedUser = $this->getUser();

        if ($authenticatedUser === null) {
            throw (new OAuthException(Response::HTTP_NOT_FOUND, 'There was an error modifying the user'))
                ->setErrors(['User is not logged in']);
        }

        $authenticatedUser->setApiToken('this is terrible');

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($authenticatedUser);
            $entityManager->flush();
            $entityManager->clear();
        } catch (Exception $e) {
            $error = $authenticatedUser !== null && in_array('ROLE_ADMIN', $authenticatedUser->getRoles())
                ? $e->getMessage()
                : "There was a problem saving the user: {$authenticatedUser->getEmail()}";
            throw (new UserModificationException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors([$error]);
        }

        return new JsonResponse(
            [
                'access_token' => $authenticatedUser->getApiToken(),
                'token_type' => 'bearer',
            ],
            200,
            [
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ]
        );
    }

    /**
     * @return array{ email: ?non-falsy-string, password: ?string, csrf_token: ?string }
     */
    private function parseInputs(Request $request): array
    {
        $requestData = json_decode($request->getContent(), true); // $request->request->all();

        return [
            'email' => UserInputStrings::trimMb4String($requestData['email'] ?? '') ?: null,
            'password' => !empty($requestData['password']) ? (string)$requestData['password'] : null,
            'csrf_token' => !empty($requestData['token']) ? (string)$requestData['token'] : null,
        ];
    }
}
