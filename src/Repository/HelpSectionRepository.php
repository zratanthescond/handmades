<?php

namespace App\Repository;

use App\Entity\HelpSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HelpSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpSection[]    findAll()
 * @method HelpSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpSection::class);
    }

    // /**
    //  * @return HelpSection[] Returns an array of HelpSection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HelpSection
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
