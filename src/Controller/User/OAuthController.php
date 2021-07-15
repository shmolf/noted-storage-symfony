<?php

namespace App\Controller\User;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Exception\OAuthException;
use App\Exception\UserModificationException;
use App\Security\TokenAuthority;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class OAuthController extends AbstractController
{
    private const TOKEN_ACCESS_LIFESPAN = 3600; // 60 seconds X 60 minutes
    private const TOKEN_REFRESH_LIFESPAN = 10800; // 60 seconds X 60 minutes X 3 hours

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

    /**
     * This function will check for a provided App token. If non provided, should redirect to the OAuth Login.
     */
    public function oAuthRegister(Request $request, TokenAuthority $tokenAuthority): Response
    {
        $appToken = $request->get('appToken');
        $returnUrl = $request->get('returnUrl');

        if ($appToken === null) {
            return $this->redirectToRoute('oauth.login', ['returnUrl' => $returnUrl]);
        }

        $user = $tokenAuthority->validateToken($appToken);

        if ($user === null) {
            return $this->redirectToRoute('oauth.login', ['returnUrl' => $returnUrl]);
        }

        return new RedirectResponse(
            $returnUrl,
            Response::HTTP_FOUND,
            [TokenAuthority::HEADER_APP_TOKEN => $appToken]
        );
    }

    // public function getRefreshToken(Request $request, TokenAuthority $tokenAuthority): Response
    // {
    //     $refreshToken = new RefreshToken();
    //     $refreshToken->setAppToken()
    //     http_build_query([
    //         'access_token' => 'some token',
    //         'expires_in' => 86400,
    //         'refresh_token' => 'some token',
    //         'refresh_token_expires_in' =>  525600,
    //     ]);
    // }

    // public function getAccessToken(Request $request, TokenAuthority $tokenAuthority): Response
    // {
    //     $refreshToken
    //     http_build_query([
    //         'access_token' => 'some token',
    //         'expires_in' => 86400,
    //         'refresh_token' => 'some token',
    //         'refresh_token_expires_in' =>  525600,
    //     ]);
    // }
}
