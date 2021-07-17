<?php

namespace App\Repository;

use App\Entity\AppToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AppToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppToken[]    findAll()
 * @method AppToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppToken::class);
    }

    // /**
    //  * @return AppToken[] Returns an array of AppToken objects
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
    public function findOneBySomeField($value): ?AppToken
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
