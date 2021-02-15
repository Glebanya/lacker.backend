<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    /**
     * @Route("/menu/{id}", name="menu_get", methods={"GET"})
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function getMenu(string $id,Request $request): Response
    {

    }

    /**
     * @Route("/menu", name="menu_add", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function addMenu(Request $request): Response
    {

    }

    /**
     * @Route("/menu/{id}", name="menu_delete", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function deleteMenu(Request $request): Response
    {

    }

    /**
     * @Route("/menu/{id}", name="menu_update", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function updateMenu(Request $request): Response
    {

    }
}
