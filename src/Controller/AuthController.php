<?php

namespace App\Controller;

use Exception;
use App\Utils\Exception\Oauth\LoginException;
use App\Utils\Login\PureStaffLoginObject;
use App\Utils\Security\JWTObjectSigner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Login\GoogleUserLoginObject;

class AuthController extends AbstractController
{
    private function getContent(string $content) : ?array
    {
        if ($content <> "" && !$data = json_decode($content,true))
        {
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
            }
        }
        return $data ?? [];
    }

    /**
     * @param Request $request
     * @param GoogleUserLoginObject $object
     * @return Response
     * @throws LoginException|Exception
     */
    #[Route('/public/auth/google', name: 'auth_google', methods: ['POST'])]
    public function authGoogle(Request $request, GoogleUserLoginObject $object): Response
    {
        if ($user = $object->setData($this->getContent($request->getContent()))->findUser() ?? $object->createUser())
        {
            return $this->json([
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getUsername(),
                    'access_token' => (new JWTObjectSigner([
                        'type' => 'client',
                        'user_id' => $user->getId(),
                        'rand' => md5(random_bytes(32),true)
                    ]))->sign(),
                ]
            ]);
        }
        throw new BadRequestHttpException('invalid json body: ');
    }

    /**
     * @param Request $request
     * @param PureStaffLoginObject $object
     * @return Response
     * @throws LoginException|Exception
     */
    #[Route('/public/auth/staff', name: 'auth_staff', methods: ['POST'])]
    public function authStaff(Request $request, PureStaffLoginObject $object): Response
    {
        if ($user = $object->setData($this->getContent($request->getContent()))->findUser())
        {
            return $this->json([
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getUsername(),
                    'access_token' => (new JWTObjectSigner([
                        'type' => 'staff',
                        'user_id' => $user->getId(),
                        'rand' => md5(random_bytes(32),true)
                    ]))->sign(),
            ]]);
        }
        throw new BadRequestHttpException('invalid json body: ');
    }
}
