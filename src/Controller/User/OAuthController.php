<?php

namespace App\Controller\User;

use App\Entity\AccessToken;
use App\Entity\AppToken;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Exception\OAuthException;
use App\Exception\UserModificationException;
use App\Repository\AccessTokenRepository;
use App\Repository\RefreshTokenRepository;
use App\TokenAuthority\AppTokenAuthority;
use App\TokenAuthority\RefreshTokenAuthority;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;

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

    /**
     * @psalm-suppress UnusedVariable
     */
    public function refreshToken(Request $request, RouterInterface $router): Response {
        /** @var EntityManagerInterface */
        $entityManager = $this->getDoctrine()->getManager();
        $refreshTokenAuthority = new RefreshTokenAuthority($entityManager);
        $curRefreshTokenString = $request->headers->get($refreshTokenAuthority::HEADER_TOKEN, '');
        // Should either be 'refreshToken' or 'accessToken'
        $tokenType = $request->query->get('grant_type');

        $curRefreshToken = $refreshTokenAuthority->validateToken($curRefreshTokenString);
        if ($curRefreshToken === null) {
            throw new HttpException(Response::HTTP_FORBIDDEN, "Provided refresh token is either expired or invalid.");
        }

        $tokenRepo = null;
        $refreshUri = null;

        switch($tokenType) {
            case 'refreshToken':
                /** @var RefreshTokenRepository */
                $tokenRepo = $entityManager->getRepository(RefreshToken::class);
                $tokenRepo->invalidateToken($curRefreshTokenString);
                $refreshUri = $router->generate('oauth.token.refresh');

                break;
            case 'accessToken':
                /** @var AccessTokenRepository */
                $tokenRepo = $entityManager->getRepository(AccessToken::class);
                $refreshUri = $router->generate('oauth.token.access');

                break;
            default:
                throw new HttpException(Response::HTTP_NOT_FOUND, "Grant Type ({$tokenType}) is not supported.");
        }

        $originalTokenHost = $curRefreshToken->getHost();
        $originalTokenuser = $curRefreshToken->getUser();
        $newToken = $tokenRepo->createToken($originalTokenuser, $originalTokenHost);
        $expiration = ($newToken->getExpirationDate() ?? new DateTime('now', new DateTimeZone('UTC')))
            ->format('Y-m-d H:i:s');

        return new JsonResponse([
            'token' => $newToken->getToken(),
            'expiration' => $expiration,
            'uri' => $refreshUri,
        ]);
    }
}
