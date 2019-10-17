<?php

namespace App\Repository;

use App\Entity\HardMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HardMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method HardMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method HardMap[]    findAll()
 * @method HardMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HardMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HardMap::class);
    }

    // /**
    //  * @return HardMap[] Returns an array of HardMap objects
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
    public function findOneBySomeField($value): ?HardMap
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
