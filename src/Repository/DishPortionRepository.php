<?php

namespace App\Repository;

use App\Entity\DishPortion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DishPortion|null find($id, $lockMode = null, $lockVersion = null)
 * @method DishPortion|null findOneBy(array $criteria, array $orderBy = null)
 * @method DishPortion[]    findAll()
 * @method DishPortion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishPortionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DishPortion::class);
    }

    // /**
    //  * @return DishPortion[] Returns an array of DishPortion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DishPortion
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
