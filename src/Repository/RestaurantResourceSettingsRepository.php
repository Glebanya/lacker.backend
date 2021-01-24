<?php

namespace App\Repository;

use App\Entity\RestaurantResourceSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RestaurantResourceSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantResourceSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantResourceSettings[]    findAll()
 * @method RestaurantResourceSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantResourceSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantResourceSettings::class);
    }

    // /**
    //  * @return RestaurantResourceSettings[] Returns an array of RestaurantResourceSettings objects
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
    public function findOneBySomeField($value): ?RestaurantResourceSettings
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
