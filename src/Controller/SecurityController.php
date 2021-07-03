<?php

namespace App\Controller;

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

    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
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

        $redirect = $request->query->get('redirect') ?? $request->headers->get('referer');

        if ($redirect === null) {
            throw new Exception('Missing redirect on successful login');
        }

        return $this->render('security/oauth-login.html.twig', [
            'crsfId' => self::CRSF_LOGIN_OAUTH_TKN,
            'redirect' => $redirect,
        ]);
    }
}
