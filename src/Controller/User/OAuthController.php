<?php

namespace App\Controller\User;

use App\Dto\AppTokenDto;
use App\Entity\AppToken;
use App\Entity\User;
use App\Exception\OAuthException;
use App\Exception\UserModificationException;
use App\Security\TokenAuthority;
use App\Utility\QoL;
use App\Utility\UserInputStrings;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @method User|null getUser()
 */
class OAuthController extends AbstractController
{
    private const CRSF_GENERATE_APP_TKN = 'generate-token';

    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function appTokenList(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        $appTokens = array_reduce(
            $user->getAppTokens()->toArray(),
            fn(array $tokens, AppToken $token) => QoL::arrPush($tokens, new AppTokenDto($token)),
            /** @var AppTokenDto[] */
            []
        );

        return $this->render('account/app-tokens.html.twig', [
            'tokens' => $appTokens,
            'crsfId' => self::CRSF_GENERATE_APP_TKN,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createAppToken(Request $request, TokenAuthority $tokenAuthority): Response
    {
        $crsfToken = $request->request->get('_csrf_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CRSF_GENERATE_APP_TKN, $crsfToken))) {
            throw new InvalidCsrfTokenException();
        }

        // We want to remove the token from the session, so the user can't just refresh the page to create new tokens.
        $this->csrfTokenManager->removeToken(self::CRSF_GENERATE_APP_TKN);

        $tokenName = UserInputStrings::trimMb4String($request->request->get('name') ?? '');
        $tokenExpiration = $request->request->get('expiration');
        $tokenExpiration = empty($tokenExpiration) ? null : new DateTime($tokenExpiration);

        if (mb_strlen($tokenName) === 0) {
            return $this->redirectToRoute('account.oauth.app.list', [], Response::HTTP_TEMPORARY_REDIRECT);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        $tokenEntity = $tokenAuthority->createToken($user, $tokenName, $tokenExpiration);

        return $this->render('account/new-app-token.html.twig', [
            'name' => $tokenEntity->getName(),
            'token' => $tokenEntity->getAuthorizationToken(),
        ]);
    }

    public function deleteAppToken(string $uuid): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        /** @var AppToken|false */
        /** @psalm-suppress UnnecessaryVarAnnotation */
        $token = $user->getAppTokens()->filter(fn(AppToken $token) => $token->getUuid() === $uuid)->first();

        if ($token instanceof AppToken) {
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
                'access_token' => 'fake token',
                'token_type' => 'bearer',
            ],
            200,
            [
                'Cache-Control' => 'no-store',
                'Pragma' => 'no-cache',
            ]
        );
    }
}
