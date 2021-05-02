<?php

namespace App\Security\Login;

use App\Entity\User;
use App\Repository\UserRepository;
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
	 */
	public function __construct(
		private Google_Client $googleApiObject,
		private UserRepository $userRepository,
		private UserPasswordEncoderInterface $encoder
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
			return $this->userRepository->findByGoogleClient($params['sub'])
				??
				(new User($params))->setGoogleId($params['sub'])->setPassword(sha1(random_bytes(32), true));
		}

		return null;
	}
}