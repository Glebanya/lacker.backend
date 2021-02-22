<?php

namespace App\Controller;

use App\Api\Auth\AuthSignerObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\Auth\Signup;


class AuthController extends AbstractController {

    /**
     * @Route("/auth/{code}", name="auth",methods={"POST"})
     * @param string $code
     * @param Request $request
     * @param Signup\SignUpFactory $factory
     * @return Response
     */
    public function register(string $code,Request $request, Signup\SignUpFactory $factory): Response {

        $manager = $this->getDoctrine()->getManager();
        $signUpObject = $factory->create($code)->setData($request->request->all());
        if (!$user = $signUpObject->findUser()) {
            $manager->persist($user = $signUpObject->createUser());
            $manager->flush();
        }
        return $this->json([
            'access_token' => AuthSignerObject::create()
                ->setParams(['user_id' => $user->getId()])
                ->sign()
        ]);
    }
}
