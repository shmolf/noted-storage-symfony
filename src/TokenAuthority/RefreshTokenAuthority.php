<?php

namespace App\TokenAuthority;

use App\Entity\RefreshToken;
use App\Exception\RefreshTokenException;
use App\Utility\Random;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenAuthority implements TokenAuthority
{
    private const TOKEN_LIFESPAN = 10800; // 60 seconds X 60 minutes X 3 hours

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(UserInterface $user): RefreshToken
    {
        $now = new DateTime();
        $expiration = clone $now;
        $expiration->modify('+' . self::TOKEN_LIFESPAN . ' seconds');

        $tokenEntity = new RefreshToken();
        $tokenEntity
            ->setExpirationDate($expiration)
            ->setCreationDate($now)
            ->setToken(Random::createString(256, [Random::ALPHA_NUM]));

        /** @psalm-suppress ArgumentTypeCoercion */
        $tokenEntity->setUser($user);

        try {
            $this->em->persist($tokenEntity);
            $this->em->flush();
            $this->em->clear();
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
