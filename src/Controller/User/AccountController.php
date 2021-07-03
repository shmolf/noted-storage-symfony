<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\UserCreationException;
use App\Exception\UserModificationException;
use App\Utility\UserInputStrings;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        /** @psalm-suppress UnnecessaryVarAnnotation */
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

    /**
     * @return array{ email: ?non-falsy-string, password: ?string, csrf_token: ?string }
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
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
