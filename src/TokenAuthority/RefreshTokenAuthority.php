<?php

namespace App\TokenAuthority;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Exception\RefreshTokenException;
use App\Utility\Random;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenAuthority implements TokenAuthority
{
    private const TOKEN_LIFESPAN = 5184000; // 60 sec X 60 min X 24 hr X 60 days

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(User $user): RefreshToken
    {
        $now = new DateTime();
        $expiration = clone $now;
        $expiration->modify('+' . self::TOKEN_LIFESPAN . ' seconds');

        $tokenEntity = new RefreshToken();
        $tokenEntity
            ->setExpirationDate($expiration)
            ->setCreationDate($now)
            ->setToken(Random::createString(256, [Random::ALPHA_NUM]));

        $user->addRefreshToken($tokenEntity);

        try {
            $this->em->persist($tokenEntity);
            $this->em->persist($user);
            $this->em->flush();
        } catch (Exception $e) {
            throw (new RefreshTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the refresh token', $e->getMessage()]);
        }

        return $tokenEntity;
    }

    public function validateToken(string $tokenString): ?RefreshToken
    {
        /** @var RefreshToken|null */
        $token = $this->em->getRepository(RefreshToken::class)->findOneBy(['token' => $tokenString]);
        return $token === null || $token->getExpirationDate() <= new DateTime() ? null : $token;
    }
}
