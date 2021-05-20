<?php

namespace App\Security\Login;

use App\Entity\Staff;
use App\Repository\StaffRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StaffLoginService
{

	public function __construct(
		private StaffRepository $repository,
		private UserPasswordEncoderInterface $encoder
	)
	{
	}

	/**
	 * @param string $email
	 * @param string $password
	 *
	 * @return Staff|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findUser(string $email, string $password): Staff|null
	{
		if ($user = $this->repository->findByMail($email))
		{
			if ($this->encoder->isPasswordValid($user, $password))
			{
				return $user;
			}
		}

		return null;
	}


}