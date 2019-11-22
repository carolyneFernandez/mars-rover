<?php

namespace App\Repository;

use App\Entity\ParamMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ParamMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParamMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParamMap[]    findAll()
 * @method ParamMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParamMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParamMap::class);
    }

    // /**
    //  * @return ParamMap[] Returns an array of ParamMap objects
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
    public function findOneBySomeField($value): ?ParamMap
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
