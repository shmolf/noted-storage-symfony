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

class AccessTokenAuthority implements TokenAuthority
{
    public const TOKEN_LIFESPAN = 3600; // 60 seconds X 60 minutes
    public const HEADER_TOKEN = 'X-TOKEN-ACCESS';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function validateToken(string $tokenString): ?AccessToken
    {
        /** @var AccessToken|null */
        $token = $this->em->getRepository(AccessToken::class)->findOneBy(['token' => $tokenString]);
        return $token === null || $token->getExpirationDate() <= new DateTime() ? null : $token;
    }
}
