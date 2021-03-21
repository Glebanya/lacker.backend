<?php

namespace App\Controller;

use App\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('public/menu/{id}', name: 'get_menu',methods: ['GET'])]
    public function index($id): Response
    {
        if ($menu = $this->getDoctrine()->getManager()->getRepository(Menu::class)->find($id))
        {
            return $this->json([
                'data' => $menu->export('ru')
            ]);
        }
        throw new BadRequestException();
    }
}
