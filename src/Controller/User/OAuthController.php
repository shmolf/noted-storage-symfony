<?php

namespace App\Controller\User;

use App\Entity\AppToken;
use App\Entity\User;
use App\Exception\OAuthException;
use App\Exception\UserModificationException;
use App\TokenAuthority\AppTokenAuthority;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class OAuthController extends AbstractController
{
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
    public function oAuthRegister(Request $request, AppTokenAuthority $tokenAuthority): Response
    {
        $token = $request->get('appToken');

        if ($token === null) {
            return $this->redirectToRoute('oauth.login');
        }

        $appToken = $tokenAuthority->validateToken($token);

        if ($appToken instanceof AppToken) {
            return $this->redirectToRoute('oauth.login.success');
        }

        return $this->redirectToRoute('oauth.login');
    }

    // public function getRefreshToken(Request $request, AppTokenAuthority $tokenAuthority): Response
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

    // public function getAccessToken(Request $request, AppTokenAuthority $tokenAuthority): Response
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
