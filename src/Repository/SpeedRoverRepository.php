<?php

namespace App\Repository;

use App\Entity\SpeedRover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SpeedRover|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpeedRover|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpeedRover[]    findAll()
 * @method SpeedRover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpeedRoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpeedRover::class);
    }

    // /**
    //  * @return SpeedRover[] Returns an array of SpeedRover objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SpeedRover
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
