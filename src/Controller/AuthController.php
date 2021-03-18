<?php

namespace App\Controller;

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

    #[Route('/public/auth/google', name: 'auth_google', methods: ['POST'])]
    public function authGoogle(Request $request, GoogleUserLoginObject $object): Response
    {
        if ($user = $object->setData($this->getContent($request->getContent()))->findUser() ?? $object->createUser())
        {
            return $this->json([
                'access_token' => (new JWTObjectSigner([
                    'type' => 'client',
                    'user_id' => $user->getId(),

                ]))->sign()
            ]);
        }
        throw new BadRequestHttpException('invalid json body: ');
    }

    #[Route('/public/auth/pure', name: 'auth_google', methods: ['POST'])]
    public function authPure(Request $request, GoogleUserLoginObject $object): Response
    {
        if ($user = $object->setData($this->getContent($request->getContent()))->findUser() ?? $object->createUser())
        {
            return $this->json([
                'access_token' => (new JWTObjectSigner([
                    'type' => 'client',
                    'user_id' => $user->getId(),

                ]))->sign()
            ]);
        }
        throw new BadRequestHttpException('invalid json body: ');
    }


    #[Route('/public/auth/staff', name: 'auth_staff', methods: ['POST'])]
    public function authStaff(Request $request, PureStaffLoginObject $object): Response
    {
        if ($user = $object->setData($this->getContent($request->getContent()))->findUser())
        {
            return $this->json([
                'access_token' => (new JWTObjectSigner([
                    'type' => 'staff',
                    'user_id' => $user->getId()
                ]))->sign()
            ]);
        }
        throw new BadRequestHttpException('invalid json body: ');
    }
}
