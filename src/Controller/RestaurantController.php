<?php

namespace App\Controller;

use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    #[Route('public/restaurant/{id}/info', name: 'restaurant_info',methods: ['GET'])]
    public function info($id): Response
    {
        if ($restaurant = $this->getDoctrine()->getManager()->getRepository(Restaurant::class)->find($id))
        {
            return $this->json([
                'data' => $restaurant->export('ru')
            ]);
        }
        throw new BadRequestException();
    }
}
