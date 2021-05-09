<?php

namespace App\Controller;

use App\Security\JWTObjectSigner;
use App\Security\Login\UserLoginService;
use App\Security\Login\StaffLoginService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
	/**
	 * @param Request $request
	 * @param UserLoginService $loginService
	 *
	 * @return Response
	 * @throws Exception
	 */
	#[Route('/public/auth/google', name: 'auth_google', methods: ['POST'])]
	public function authGoogle(Request $request, UserLoginService $loginService): Response
	{
		$content = $this->getContent($request);
		if ($user = $loginService->findOrCreateUser($content['google_token']))
		{
			return $this->json([
				'data' => [
					'id' => $user->getId(),
					'email' => $user->getEmail(),
					'name' => $user->getUsername(),
					'access_token' => (new JWTObjectSigner([
						'type' => 'client',
						'user_id' => $user->getId(),
						'rand' => rand(0,721)
					]))->sign(),
				]
			]);
		}
		throw new BadRequestHttpException('invalid json body: ');
	}

	private function getContent(Request $request): ?array
	{
		if (!$data = json_decode($request->getContent() , true))
		{
			if (json_last_error() !== JSON_ERROR_NONE)
			{
				throw new BadRequestHttpException('invalid json body: '.json_last_error_msg());
			}
		}

		return $data ?? [];
	}

	/**
	 * @param Request $request
	 * @param StaffLoginService $loginService
	 *
	 * @return Response
	 * @throws Exception
	 */
	#[Route('/public/auth/staff', name: 'auth_staff', methods: ['POST'])]
	public function authStaff(Request $request, StaffLoginService $loginService): Response
	{
		$content = $this->getContent($request);
		if ($user = $loginService->findUser(email: $content['email'],password: $content['password']))
		{
			return $this->json([
				'data' => [
					'id' => $user->getId(),
					'email' => $user->getEmail(),
					'name' => $user->getUsername(),
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
