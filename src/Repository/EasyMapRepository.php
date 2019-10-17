<?php

namespace App\Repository;

use App\Entity\EasyMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EasyMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method EasyMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method EasyMap[]    findAll()
 * @method EasyMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EasyMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EasyMap::class);
    }

    // /**
    //  * @return EasyMap[] Returns an array of EasyMap objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EasyMap
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
