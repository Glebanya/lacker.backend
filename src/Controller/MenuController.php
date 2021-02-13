<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu/{id}", name="menu")
     * @param string $id
     * @return Response
     */
    public function index(string $id): Response
    {
        return $this->json([
            //'message' => $id,
        ]);
    }
    /*
    /**
     * @Route ()
     */
    /*public function menu($id)
    {

    }*/
}
