<?php

namespace App\Security\Login;

use App\Entity\Staff;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PureStaffLoginObject implements InterfaceLoginObject
{

	private array $container = [];

	public function __construct(private EntityManagerInterface $manager, private UserPasswordEncoderInterface $encoder)
	{
	}

	/**
	 * @param array $params
	 *
	 * @return InterfaceLoginObject
	 * @throws LoginException
	 */
	public function setData(array $params): InterfaceLoginObject
	{
		if (array_key_exists('email', $params))
		{
			$this->container['email'] = $params['email'];
		}
		else
		{
			throw new LoginException();
		}
		if (array_key_exists('password', $params))
		{
			$this->container['password'] = $params['password'];
		}
		else
		{
			throw new LoginException();
		}

		return $this;
	}

	/**
	 * @return UserInterface|User|Staff|null
	 */
	public function findUser(): UserInterface|User|Staff|null
	{
		if ($user = $this->manager->getRepository(Staff::class)->findByMail($this->container['email']))
		{
			if ($this->encoder->isPasswordValid($user, $this->container['business']))
			{
				return $user;
			}
		}

		return null;
	}

	public function createUser(): UserInterface|User|Staff|null
	{
		$this->manager->persist(($staff = new Staff())->setEmail($this->container['email'])
			->setPassword($this->encoder->encodePassword($staff, $this->container['password'])));
		$this->manager->flush();

		return $staff;
	}

}