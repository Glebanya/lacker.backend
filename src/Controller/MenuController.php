<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Api\Request;
use App\Entity\Stuff;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends ControllerBase
{

    /**
     * @Route("/menu/{id}", name="menu_get", methods={"GET"})
     * @param string $id
     * @return Response
     */
    public function get(string $id): Response {
        return $this->json(['hello']);
    }

    /**
     * @Route("/menu", name="menu_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response {
        $mng = $this->getDoctrine()->getManager();
        $mng->persist($menu = (new Menu())
            ->setCreationDate(new \DateTime('NOW'))
            ->setUpdateDate(new \DateTime('NOW'))
            ->setEnable(true)
        );
        $mng->flush();
        return $this->json($menu);
    }

    /**
     * @Route("/menu/{id}", name="menu_delete", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response {

    }

    /**
     * @Route("/menu/{id}", name="menu_update", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response {

    }

    #[ArrayShape(['get' => "string[]", 'update' => "array", 'delete' => "array", 'add' => "array"])]
    public function getMethodMap() : array {
        return [
            'update'=> ['type' => 'stuff','rights' => Stuff::OWNER|Stuff::ADMIN],
            'delete'=> ['type' => 'stuff','rights' => Stuff::OWNER|Stuff::ADMIN],
            'add' => ['type' => 'stuff' ,'rights' => Stuff::OWNER|Stuff::ADMIN]
        ];
    }

}
