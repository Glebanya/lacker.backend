<?php

namespace App\Repository;

use App\Entity\TableReserve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TableReserve|null find($id, $lockMode = null, $lockVersion = null)
 * @method TableReserve|null findOneBy(array $criteria, array $orderBy = null)
 * @method TableReserve[]    findAll()
 * @method TableReserve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableReserveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TableReserve::class);
    }

    // /**
    //  * @return TableReserve[] Returns an array of TableReserve objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TableReserve
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
