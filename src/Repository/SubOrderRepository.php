<?php

namespace App\Repository;

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

	public function getSuborders(Restaurant $restaurant, bool $checked, int $offset, int $limit)
	{
		return $this->createQueryBuilder('sub_order')
			->leftJoin('sub_order.baseOrder','base_order')
			->andWhere('sub_order.checked = :checked')
			->andWhere('base_order.restaurant = :rest')
			->orderBy('sub_order.update_date','DESC')
			->setParameter('checked',$checked)
			->setParameter('rest', $restaurant)
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getQuery()
			->getResult();
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
