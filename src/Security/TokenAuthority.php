<?php

namespace App\Security;

use App\Entity\AppToken;
use App\Exception\AppTokenException;
use App\Utility\Random;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenAuthority
{
    public const SESSION_OAUTH_APP_TOKEN = 'oauth-generated-app-token';
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(UserInterface $user, string $tokenName, ?DateTime $tokenExpiration): AppToken
    {
        $now = new DateTime();
        $tokenEntity = new AppToken();
        $tokenEntity
            ->setName($tokenName)
            ->setExpirationDate($tokenExpiration)
            ->setCreatedDate($now)
            ->setUuid(Uuid::uuid4()->toString())
            ->setAuthorizationToken(Random::createString(42, [Random::ALPHA_NUM]));

        /** @psalm-suppress ArgumentTypeCoercion */
        $tokenEntity->setUser($user);

        try {
            $this->em->persist($tokenEntity);
            $this->em->flush();
            $this->em->clear();
        } catch (Exception $e) {
            throw (new AppTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the token', $e->getMessage()]);
        }

        return $tokenEntity;
    }
}