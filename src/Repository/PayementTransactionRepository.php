<?php

namespace App\Repository;

use App\Entity\PayementTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PayementTransaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayementTransaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayementTransaction[]    findAll()
 * @method PayementTransaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayementTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayementTransaction::class);
    }

    // /**
    //  * @return PayementTransaction[] Returns an array of PayementTransaction objects
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
    public function findOneBySomeField($value): ?PayementTransaction
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
