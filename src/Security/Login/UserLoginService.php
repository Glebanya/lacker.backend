<?php

namespace App\Security\Login;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
	 * @param EntityManagerInterface $manager
	 */
	public function __construct(
		private Google_Client $googleApiObject,
		private UserRepository $userRepository,
		private UserPasswordEncoderInterface $encoder,
		private EntityManagerInterface $manager,
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
				$user = (
					new User([
						'name' => $params['given_name'],
						'familyName' => $params['family_name'],
						'email' => $params['email'],
						'picture' => $params['picture']
					])
					)->setGoogleId($params['sub'])
					->setPassword(sha1(random_bytes(32), true));
				$this->manager->persist($user);
				$this->manager->flush();
			}
			return $user;
		}

		return null;
	}
}