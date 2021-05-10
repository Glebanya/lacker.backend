<?php

namespace App\Repository;

use App\Entity\Staff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method Staff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Staff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Staff[]    findAll()
 * @method Staff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffRepository extends ServiceEntityRepository implements UserLoaderInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, Staff::class);
	}

	public function loadUserByUsername(string $username)
	{
		return $this->matching(
			Criteria::create()
				->where(
					Criteria::expr()->eq('id',$username)
				)
				->andWhere(
					Criteria::expr()->eq('deleted',false)
				)
		)->first();
	}

	/**
	 * @param $mail
	 *
	 * @return Staff|null
	 * @throws NonUniqueResultException
	 */
	public function findByMail(string $mail): ?Staff
	{
		return $this->createQueryBuilder('user')->andWhere('user.email = :email')->setParameter('email', $mail)
			->getQuery()->getOneOrNullResult();
	}
}
