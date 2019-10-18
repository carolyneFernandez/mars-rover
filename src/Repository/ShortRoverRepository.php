<?php

namespace App\Repository;

use App\Entity\ShortRover;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShortRover|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortRover|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortRover[]    findAll()
 * @method ShortRover[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortRoverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortRover::class);
    }

    // /**
    //  * @return ShortRover[] Returns an array of ShortRover objects
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
    public function findOneBySomeField($value): ?ShortRover
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
