<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="login",methods={"GET"})
     */
    public function login(): Response
    {

    }

    /**
     * @Route("/logout/{token}", name="logout", methods={"DELETE"})
     */
    public function index($token): Response
    {

    }

    /**
     * @Route("/register", name="auth",methods={"POST"})
     */
    public function register(): Response
    {

    }

}
