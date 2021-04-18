<?php

namespace App\Repository;

use App\Entity\BaseObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BaseObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseObject[]    findAll()
 * @method BaseObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseObject::class);
    }

    public function findByIds($ids)
    {
        return ($builder = $this->createQueryBuilder('base'))
            ->where($builder->expr()->in('id',$ids))
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return BaseObject[] Returns an array of BaseObject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BaseObject
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
