<?php

namespace App\Repository;

use App\Entity\OrderReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderReview[]    findAll()
 * @method OrderReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderReview::class);
    }

    // /**
    //  * @return OrderReview[] Returns an array of OrderReview objects
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
    public function findOneBySomeField($value): ?OrderReview
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
