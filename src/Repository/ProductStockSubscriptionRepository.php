<?php

namespace App\Repository;

use App\Entity\ProductStockSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductStockSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductStockSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductStockSubscription[]    findAll()
 * @method ProductStockSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductStockSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductStockSubscription::class);
    }

    // /**
    //  * @return ProductStockSubscription[] Returns an array of ProductStockSubscription objects
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
    public function findOneBySomeField($value): ?ProductStockSubscription
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
