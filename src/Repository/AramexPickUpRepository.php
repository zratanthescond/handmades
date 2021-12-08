<?php

namespace App\Repository;

use App\Entity\AramexPickUp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AramexPickUp|null find($id, $lockMode = null, $lockVersion = null)
 * @method AramexPickUp|null findOneBy(array $criteria, array $orderBy = null)
 * @method AramexPickUp[]    findAll()
 * @method AramexPickUp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AramexPickUpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AramexPickUp::class);
    }

    // /**
    //  * @return AramexPickUp[] Returns an array of AramexPickUp objects
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
    public function findOneBySomeField($value): ?AramexPickUp
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
