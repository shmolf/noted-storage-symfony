<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Exception\RefreshTokenException;
use App\TokenAuthority\RefreshTokenAuthority;
use App\Utility\Random;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function createToken(?User $user, ?string $host): RefreshToken
    {
        if ($user === null) {
            throw new RefreshTokenException(Response::HTTP_BAD_REQUEST, 'User is not set');
        }

        $entityManager = $this->getEntityManager();
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $expiration = clone $now;
        $expiration->modify('+' . RefreshTokenAuthority::TOKEN_LIFESPAN . ' seconds');

        $tokenEntity = new RefreshToken();
        $tokenEntity
            ->setExpirationDate($expiration)
            ->setCreationDate($now)
            ->setToken(Random::createString(256, [Random::ALPHA_NUM]))
            ->setIsValid(true)
            ->setHost($host);

        $user->addRefreshToken($tokenEntity);

        try {
            $entityManager->persist($tokenEntity);
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (Exception $e) {
            throw (new RefreshTokenException(Response::HTTP_BAD_REQUEST, 'There was an error creating the token'))
                ->setErrors(['There was an error creating the refresh token', $e->getMessage()]);
        }

        return $tokenEntity;
    }

    public function invalidateToken(string $tokenStr): void
    {
        $tokenEntity = $this->findOneBy(['token' => $tokenStr, 'isValid' => true]);

        if ($tokenEntity instanceof RefreshToken) {
            $tokenEntity->setIsValid(false);

            try {
                $entityManager = $this->getEntityManager();
                $entityManager->persist($tokenEntity);
                $entityManager->flush();
            } catch (Exception $e) {
                $errors = [$e->getMessage()];
                $prevError = $e->getPrevious();

                if ($prevError instanceof Throwable) {
                    $errors[] = $prevError->getMessage();
                }

                throw (new RefreshTokenException(
                    Response::HTTP_BAD_REQUEST,
                    'There was an error while trying to invalidate the token'
                ))->setErrors($errors);
            }
        }
    }

    // /**
    //  * @return RefreshToken[] Returns an array of RefreshToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RefreshToken
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
