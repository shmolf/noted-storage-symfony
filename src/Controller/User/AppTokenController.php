<?php

namespace App\Controller\User;

use App\Dto\AppTokenDto;
use App\Entity\AppToken;
use App\Entity\User;
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
class AppTokenController extends AbstractController
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
            return $this->redirectToRoute('appToken.list');
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
}
