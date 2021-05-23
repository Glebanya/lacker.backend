<?php

namespace App\Controller;

use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Security\JWTObjectSigner;
use App\Security\Login\UserLoginService;
use App\Security\Login\StaffLoginService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
	use ApiTrait;

	/**
	 * @param Request $request
	 * @param UserLoginService $loginService
	 * @param ApiService $apiService
	 * @param Serializer $serializer
	 *
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/public/auth/google', name: 'auth_google', methods: ['POST'])]
	public function authGoogle(
		Request $request,
		UserLoginService $loginService,
		ApiService $apiService,
		Serializer $serializer
	) : JsonResponse
	{
		$content = $this->getContent($request);
		if ($user = $loginService->findOrCreateUser($content['google_token']))
		{
			return $this->json([
					'data' =>
						$serializer->serialize($apiService->buildApiEntityObject($user)) +
						[
							'access_token' => (new JWTObjectSigner([
								'type' => 'client',
								'user_id' => $user->getId(),
								'rand' => rand(0,721)])
							)->sign(),
						]
			]);
		}
		throw new BadRequestHttpException('invalid google token');
	}

	/**
	 * @param Request $request
	 * @param StaffLoginService $loginService
	 * @param ApiService $apiService
	 * @param Serializer $serializer
	 *
	 * @return JsonResponse
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	#[Route('/public/auth/staff', name: 'auth_staff', methods: ['POST'])]
	public function authStaff(
		Request $request,
		StaffLoginService $loginService,
		ApiService $apiService,
		Serializer $serializer
	): JsonResponse
	{
		$content = $this->getContent($request);
		if ($user = $loginService->findUser(email: $content['email'],password: $content['password']))
		{
			return $this->json([
					'data' =>
						$serializer->serialize($apiService->buildApiEntityObject($user)) +
						[
							'access_token' => (new JWTObjectSigner([
								'type' => 'staff',
								'user_id' => $user->getId(),
								'rand' => rand(0,721)
							]))->sign(),
						]

			]);
		}
		throw new BadRequestHttpException('unknown user');
	}
}
