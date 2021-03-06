<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Api\Request;
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
        $user = $this->getAccessController()->getUser();
        return $this->json([$user->getMail()]);
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

    function getNonPublicMethods() : array {
        return [
            'update',
            'delete',
            'add'
        ];
    }
}
