<?php

namespace App\Repository;

use App\Entity\OrderSpecialDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderSpecialDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderSpecialDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderSpecialDiscount[]    findAll()
 * @method OrderSpecialDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderSpecialDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderSpecialDiscount::class);
    }

    // /**
    //  * @return OrderSpecialDiscount[] Returns an array of OrderSpecialDiscount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderSpecialDiscount
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
