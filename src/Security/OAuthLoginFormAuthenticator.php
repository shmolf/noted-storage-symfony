<?php

namespace App\Security;

use App\Controller\SecurityController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\TokenAuthority\AccessTokenAuthority;
use App\TokenAuthority\RefreshTokenAuthority;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class OAuthLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private UserRepository $userRepository;
    private RouterInterface $router;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'oauth.login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken(SecurityController::CRSF_LOGIN_OAUTH_TKN, $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ): Response {
        /** @var User */
        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new Exception('User not available');
        }

        $accessToken = (new AccessTokenAuthority($this->entityManager))->createToken($user);
        $refreshToken = (new RefreshTokenAuthority($this->entityManager))->createToken($user);

        if ($request->hasSession()) {
            $request->getSession()->invalidate();
        }

        return new JsonResponse([
            'refreshToken' => [
               'token' => $refreshToken->getToken(),
               'expiration' => $refreshToken->getExpirationDate(),
               'uri' => $this->router->generate('oauth.token.refresh'),
            ],
            'accessToken' => [
                'token' => $accessToken->getToken(),
                'expiration' => $refreshToken->getExpirationDate(),
                'uri' => $this->router->generate('oauth.token.access'),
            ],
        ]);
    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('login');
    }
}
