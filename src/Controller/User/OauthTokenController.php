<?php

namespace App\Controller\User;

use App\Dto\OauthTokenDto;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Utility\QoL;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 */
class OauthTokenController extends AbstractController
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function oauthTokenList(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new Exception('User is not logged in');
        }

        $refreshTokens = array_reduce(
            $user->getRefreshTokens()->toArray(),
            fn(array $tokens, RefreshToken $token) => QoL::arrPush($tokens, new OauthTokenDto($token)),
            /** @var OauthTokenDto[] */
            []
        );

        return $this->render('account/oauth-tokens.html.twig', [
            'tokens' => $refreshTokens,
        ]);
    }

    // public function deleteOauthToken(string $uuid): JsonResponse
    // {
    //     $user = $this->getUser();
    //     if (!$user instanceof User) {
    //         throw new Exception('User is not logged in');
    //     }

    //     /** @var RefreshToken|false */
    //     /** @psalm-suppress UnnecessaryVarAnnotation */
    //     $token = $user->getRefreshTokens()->filter(fn(RefreshToken $token) => $token->getUuid() === $uuid)->first();

    //     if ($token instanceof RefreshToken) {
    //         $user->removeRefreshToken($token);
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($user);
    //         $entityManager->flush();
    //         $entityManager->clear();

    //         return new JsonResponse();
    //     }

    //     return new JsonResponse([], Response::HTTP_NOT_FOUND);
    // }
}
