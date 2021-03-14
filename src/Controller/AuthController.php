<?php

namespace App\Controller;

use App\Api\Auth\AuthSignerObject;
use App\Entity\Stuff;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\Auth\Signup;


class AuthController extends ControllerBase {

    /**
     * @Route("/auth/{code}", name="auth",methods={"POST"})
     * @param string $code
     * @param Signup\SignUpFactory $factory
     * @return Response
     */
    public function register(string $code, Signup\SignUpFactory $factory): Response {

        $manager = $this->getDoctrine()->getManager();
        $signUpObject = $factory->create($code)->setData($this->getApiManager()->getRequest()->getContent());
        if (!$user = $signUpObject->findUser()) {
            $manager->persist($user = $signUpObject->createUser());
            $manager->flush();
        }
        return $this->json([
            'access_token' => AuthSignerObject::create()
                ->setParams([
                    'type' => $user instanceof Stuff? 'stuff' : 'client',
                    'user_id' => $user->getId()
                ])
                ->sign()
        ]);
    }

    public function getMethodMap(): array {
        return [];
    }
}
