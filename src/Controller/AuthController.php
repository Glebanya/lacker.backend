<?php

namespace App\Controller;

use App\Api\Auth\AuthSignerObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Api\Auth\Signup;


class AuthController extends AbstractController
{

    /**
     * @Route("/register/{code}", name="auth",methods={"POST"})
     * @return Response
     */
    public function register(string $code,Request $request): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($userData = Signup\SignUpFactory::create($code)->setData($request->request->all())->getUserData());
        $manager->flush();
        return $this->json([
            'access_token' => AuthSignerObject::create()
                ->setParams(['user_id' => $userData->getId()])
                ->sign()
        ]);


    }

}
