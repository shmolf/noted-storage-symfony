<?php

namespace App\TokenAuthority;

use App\Entity\RefreshToken;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RefreshTokenAuthority implements TokenAuthority
{
    public const TOKEN_LIFESPAN = 5184000; // 60 sec X 60 min X 24 hr X 60 days
    public const HEADER_TOKEN = 'X-TOKEN-REFRESH';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validateToken(string $tokenString): ?RefreshToken
    {
        /** @var RefreshToken|null */
        $token = $this->em->getRepository(RefreshToken::class)->findOneBy(['token' => $tokenString, 'isValid' => true]);
        return $token === null || $token->getExpirationDate() <= new DateTime() ? null : $token;
    }
}
