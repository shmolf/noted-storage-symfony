<?php

namespace App\TokenAuthority;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Exception\AccessTokenException;
use App\Utility\Random;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessTokenAuthority implements TokenAuthority
{
    private const TOKEN_LIFESPAN = 3600; // 60 seconds X 60 minutes

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(UserInterface $user): AccessToken
    {
        $now = new DateTime();
        $expiration = clone $now;
        $expiration->modify('+' . self::TOKEN_LIFESPAN . ' seconds');

        $tokenEntity = new AccessToken();
        $tokenEntity
            ->setExpirationDate($expiration)
            ->setCreationDate($now)
            ->setToken(Random::createString(256, [Random::ALPHA_NUM]));

        $user->addAccessToken($tokenEntity);

        try {
            $this->em->persist($tokenEntity);
            $this->em->persist($user);
            $this->em->flush();
        } catch (Exception $e) {
            throw (new AccessTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the access token', $e->getMessage()]);
        }

        return $tokenEntity;
    }

    public function validateToken(string $tokenString): ?AccessToken
    {
        /** @var AccessToken|null */
        $token = $this->em->getRepository(AccessToken::class)->findOneBy(['token' => $tokenString]);
        return $token === null || $token->getExpirationDate() <= new DateTime() ? null : $token;
    }
}
