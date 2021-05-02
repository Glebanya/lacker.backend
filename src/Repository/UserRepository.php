<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	/**
	 * @param $mail
	 *
	 * @return User|null
	 * @throws NonUniqueResultException
	 */
	public function findByMail(string $mail): ?User
	{
		return $this->createQueryBuilder('user')->andWhere('user.email = :email')->setParameter('email', $mail)->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param string $googleId
	 *
	 * @return User|null
	 * @throws NonUniqueResultException
	 */
	public function findByGoogleClient(string $googleId): ?User
	{
		return $this->createQueryBuilder('user')->Where('user.googleId = :googleId')
			->setParameter('googleId', $googleId)->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param string $username
	 *
	 * @return UserInterface|null
	 * @throws NonUniqueResultException
	 */
	public function loadUserByUsername(string $username): ?UserInterface
	{
		return $this->createQueryBuilder('user')->where('user.id = :id')->setParameter('id', $username)->getQuery()->getOneOrNullResult();
	}
}
