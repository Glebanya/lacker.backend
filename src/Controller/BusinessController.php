<?php

namespace App\Controller;

use App\Entity\Business;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BusinessController extends ControllerBase
{

    /**
     * @Route("/business", name="business")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request) : Response {
        $mng = $this->getDoctrine()->getManager();
        $mng->persist($business = (new Business()));
        $mng->flush();
        return $this->json([
            'id' => $business->getId()
        ]);
    }
}
