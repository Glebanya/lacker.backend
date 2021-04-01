<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository
    )
    {}

    private function getContent(Request $request) : ?array
    {
        $content = $request->getContent();
        if ($content <> "" && !$data = json_decode($content,true))
        {
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
            }
        }
        return $data ?? [];
    }

    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {

    }

    #[Route('/profile/make', name:'profile_orders')]
    public function makeOrder()
    {
        if($user = $this->getUser())
        {

        }
    }

    #[Route('/profile/current', name:'profile_orders')]
    public function getCurrentOrder()
    {

    }

    #[Route('/profile/orders', name:'profile_orders')]
    public function getOrders()
    {
        
    }
}
