<?php

namespace App\Repository;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Exception\AccessTokenException;
use App\TokenAuthority\AccessTokenAuthority;
use App\Utility\Random;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    // /**
    //  * @return AccessToken[] Returns an array of AccessToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccessToken
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(?User $user, ?string $host): AccessToken
    {
        if ($user === null) {
            throw new AccessTokenException(Response::HTTP_BAD_REQUEST, 'User is not set');
        }

        $entityManager = $this->getEntityManager();
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $expiration = clone $now;
        $expiration->modify('+' . AccessTokenAuthority::TOKEN_LIFESPAN . ' seconds');

        $tokenEntity = new AccessToken();
        $tokenEntity
            ->setExpirationDate($expiration)
            ->setCreationDate($now)
            ->setToken(Random::createString(256, [Random::ALPHA_NUM]))
            ->setHost($host);

        $user->addAccessToken($tokenEntity);

        try {
            $entityManager->persist($tokenEntity);
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $e) {
            throw (new AccessTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the access token', $e->getMessage()]);
        }

        return $tokenEntity;
    }
}
