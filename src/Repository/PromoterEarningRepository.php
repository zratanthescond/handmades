<?php

namespace App\Repository;

use App\Entity\PromoterEarning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoterEarning|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoterEarning|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoterEarning[]    findAll()
 * @method PromoterEarning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoterEarningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoterEarning::class);
    }

    // /**
    //  * @return PromoterEarning[] Returns an array of PromoterEarning objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PromoterEarning
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
