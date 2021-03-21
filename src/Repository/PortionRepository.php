<?php

namespace App\Repository;

use App\Entity\Portion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Portion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portion[]    findAll()
 * @method Portion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Portion::class);
    }

    // /**
    //  * @return Portion[] Returns an array of Portion objects
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
    public function findOneBySomeField($value): ?Portion
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
