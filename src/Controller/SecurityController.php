<?php

namespace App\Controller;

use App\TokenAuthority\AccessTokenAuthority;
use App\TokenAuthority\RefreshTokenAuthority;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public const CRSF_LOGIN_OAUTH_TKN = 'login-oauth';

    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    public function oAuthLogin(Request $request): Response
    {
        // We don't want library computers to share sessions...
        if ($request->hasSession()) {
            $request->getSession()->invalidate();
        }

        return $this->render('security/oauth-login.html.twig', [
            'crsfId' => self::CRSF_LOGIN_OAUTH_TKN,
        ]);
    }

    public function oAuthLoginSuccess(
        RefreshTokenAuthority $refreshAuthority,
        AccessTokenAuthority $acceessAuthority
    ): Response {
        $user = $this->getUser();

        if ($user === null) {
            throw new Exception('User is not authenticated');
        }

        $refreshToken = $refreshAuthority->createToken($user);
        $accessToken = $acceessAuthority->createToken($user);

        return $this->render('security/oauth-success.html.twig', [
            'refreshToken' => $refreshToken->getToken(),
            'accessToken' => $accessToken->getToken(),
        ]);
    }
}
