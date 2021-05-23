<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\SubOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubOrder[]    findAll()
 * @method SubOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubOrderRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, SubOrder::class);
	}

	public function getSuborders(Restaurant $restaurant, $checked, $offset, $limit)
	{
	}
    // /**
    //  * @return SubOrder[] Returns an array of SubOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SubOrder
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
