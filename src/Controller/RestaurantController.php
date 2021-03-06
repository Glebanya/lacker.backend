<?php

namespace App\Controller;

use App\Entity\Business;
use App\Entity\Restaurant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/restaurant", name="restaurant")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request) : Response {
        $mng = $this->getDoctrine()->getManager();
        $mng->persist($rest = (new Restaurant())->setBusiness($mng->getRepository(Business::class)->find($request->request->get('business_id'))));
        $mng->flush();
        return $this->json([
            'id' => $rest->getId()
        ]);
    }
}
