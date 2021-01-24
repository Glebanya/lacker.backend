<?php

namespace App\Repository;

use App\Entity\RestaurantResourceText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RestaurantResourceText|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantResourceText|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantResourceText[]    findAll()
 * @method RestaurantResourceText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantResourceTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantResourceText::class);
    }

    // /**
    //  * @return RestaurantResourceText[] Returns an array of RestaurantResourceText objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RestaurantResourceText
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
