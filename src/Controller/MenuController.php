<?php

namespace App\Controller;

use App\Api\Event\TokenEventSubscriber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController implements ITokenController
{

    private TokenEventSubscriber $subscriber;
    /**
     * @Route("/menu/{id}", name="menu_get", methods={"GET"})
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function getMenu(string $id,Request $request): Response {
        $user = $this->getAccessController()->getUser();
        return $this->json([$user->getMail()]);
    }

    /**
     * @Route("/menu", name="menu_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addMenu(Request $request): Response {

    }

    /**
     * @Route("/menu/{id}", name="menu_delete", methods={"DELETE"})
     * @param Request $request
     * @return Response
     */
    public function deleteMenu(Request $request): Response {

    }

    /**
     * @Route("/menu/{id}", name="menu_update", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function updateMenu(Request $request): Response
    {

    }

    function setAccessController(TokenEventSubscriber $accessController) {
        $this->subscriber = $accessController;
    }

    function getAccessController(): ?TokenEventSubscriber {
        return $this->subscriber;
    }
}
