<?php

namespace App\Repository;

use App\Entity\PromoterWithdrawalRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoterWithdrawalRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoterWithdrawalRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoterWithdrawalRequest[]    findAll()
 * @method PromoterWithdrawalRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoterWithdrawalRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoterWithdrawalRequest::class);
    }

    // /**
    //  * @return PromoterWithdrawalRequest[] Returns an array of PromoterWithdrawalRequest objects
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
    public function findOneBySomeField($value): ?PromoterWithdrawalRequest
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
