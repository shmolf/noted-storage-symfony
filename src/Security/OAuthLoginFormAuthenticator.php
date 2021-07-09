<?php

namespace App\Security;

use App\Controller\SecurityController;
use App\Exception\AppTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $redirect = $request->query->get('redirect');
        $redirectUrl = filter_var($redirect, FILTER_SANITIZE_URL);

        if ($redirectUrl === false) {
            throw (new AppTokenException(Response::HTTP_BAD_REQUEST, 'There was an error with the provided redirect URL'))
                ->setErrors(['Redirect URL was invalid', $redirect]);
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new Exception('Expected `UserInterface`, found a string');
        }

        $tokenName = parse_url($redirectUrl, PHP_URL_HOST);
        $tokenAuthority = new TokenAuthority($this->entityManager);
        $tokenEntity = $tokenAuthority->createToken($user, $tokenName, null);

        $request->getSession()->set(TokenAuthority::SESSION_OAUTH_APP_TOKEN, $tokenEntity->getAuthorizationToken());

        return new RedirectResponse($this->router->generate('oauth.login.success'));
    }

    protected function getLoginUrl(): string
    {
        return $this->router->generate('login');
    }
}
