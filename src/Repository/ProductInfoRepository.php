<?php

namespace App\Repository;

use App\Entity\ProductInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductInfo[]    findAll()
 * @method ProductInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductInfo::class);
    }

    // /**
    //  * @return ProductInfo[] Returns an array of ProductInfo objects
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
    public function findOneBySomeField($value): ?ProductInfo
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
