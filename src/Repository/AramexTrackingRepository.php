<?php

namespace App\Repository;

use App\Entity\AramexTracking;
use App\Service\Aramex\AramexTrackingUpdateCodeResolver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AramexTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method AramexTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method AramexTracking[]    findAll()
 * @method AramexTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AramexTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AramexTracking::class);
    }

    /**
     * @return AramexTracking[] Returns an array of AramexTracking objects
     */
    public function findUndeliveredTrackings($maxResult = 10)
    {

        return $this->createQueryBuilder('t')
            ->andWhere("t.UpdateCode <> :finalStatus")
            ->setParameter("finalStatus",  AramexTrackingUpdateCodeResolver::DELIVERED_STATUS)
            ->setmaxResults($maxResult)
            ->getquery()
            ->getresult();
    }

    // /**
    //  * @return AramexTracking[] Returns an array of AramexTracking objects
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
    public function findOneBySomeField($value): ?AramexTracking
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
