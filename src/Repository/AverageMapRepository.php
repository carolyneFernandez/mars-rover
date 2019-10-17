<?php

namespace App\Repository;

use App\Entity\AverageMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AverageMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method AverageMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method AverageMap[]    findAll()
 * @method AverageMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AverageMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AverageMap::class);
    }

    // /**
    //  * @return AverageMap[] Returns an array of AverageMap objects
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
    public function findOneBySomeField($value): ?AverageMap
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
