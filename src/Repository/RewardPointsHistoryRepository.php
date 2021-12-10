<?php

namespace App\Repository;

use App\Entity\RewardPointsHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RewardPointsHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method RewardPointsHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method RewardPointsHistory[]    findAll()
 * @method RewardPointsHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RewardPointsHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RewardPointsHistory::class);
    }

    // /**
    //  * @return RewardPointsHistory[] Returns an array of RewardPointsHistory objects
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
    public function findOneBySomeField($value): ?RewardPointsHistory
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
