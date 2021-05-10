<?php

namespace App\Security\Login;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Google_Client;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserLoginService
{
	/**
	 * UserLoginService constructor.
	 *
	 * @param Google_Client $googleApiObject
	 * @param UserRepository $userRepository
	 * @param UserPasswordEncoderInterface $encoder
	 * @param EntityManager $manager
	 */
	public function __construct(
		private Google_Client $googleApiObject,
		private UserRepository $userRepository,
		private UserPasswordEncoderInterface $encoder,
		protected EntityManager $manager
	)
	{
	}

	/**
	 * @param string $googleClient
	 *
	 * @return User|null
	 * @throws Exception
	 */
	public function findOrCreateUser(string $googleClient): User|null
	{
		if (is_array($params = $this->googleApiObject->verifyIdToken($googleClient)) && array_key_exists('sub', $params))
		{
			if (!$user = $this->userRepository->findByGoogleClient($params['sub']))
			{
				$user = (new User($params))->setGoogleId($params['sub'])->setPassword(sha1(random_bytes(32), true));
				$this->manager->persist($user);
			}
			return $user;
		}

		return null;
	}
}