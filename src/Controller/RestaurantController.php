<?php

namespace App\Controller;

use App\API\ApiObject;
use App\Entity\Dish;
use App\Entity\Menu;
use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Repository\DishRepository;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RestaurantController extends AbstractController
{

    public const VIEW = 'View';
    public const EDIT = 'Edit';
    public const DELETE = 'Delete';
    public const ADD = 'Add';

    private ApiObject $restMeta;

    public function __construct(
        private Security $security,
        private EntityManagerInterface $manager,
        private RestaurantRepository $restaurantRepository,
        private DishRepository $rep
    )
    {
    }

    private function getContent(string $content) : ?array
    {
        if ($content <> "" && !$data = json_decode($content,true))
        {
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
            }
        }
        return $data ?? [];
    }

    private function formatDishCollection(Collection $collection,string $locale) : array
    {
        return $collection->map(function (Dish $dish) use ($locale){
            return [
                'id' => $dish->getId(),
                'name' => $dish->getName()[$locale],
                'description' => $dish->getDescription()[$locale],
                'portions' => $dish->getPortions()->map(function (Portion $portion) use ($locale) {
                    return [
                        'id' => $portion->getId(),
                        'price' => $portion->getPrice()[$locale],
                        'size'=>$portion->getSize()[$locale]
                    ];
                })->getValues()
            ];
        })->getValues();
    }

    #[Route('public/restaurant/{id}/info', name: 'restaurant_dishes',methods: ['GET'])]
    public function info($id, Request $request): Response
    {
        $locale = $request->headers->get('Locale','ru');
        if ($restaurant = $this->restaurantRepository->find($id))
        {
            return $this->json([
                'data' => [
                    'time' => time(),
                    'dishes' => $this->formatDishCollection($restaurant->getValidDishes(),$locale)
                ]
            ],Response::HTTP_OK);
        }
        throw new BadRequestException();
    }

    #[Route('public/restaurant/{id}/dishes', name: 'restaurant_dishes',methods: ['GET'])]
    public function dishes($id,Request $request): Response
    {
        $locale = $request->headers->get('Locale','ru');
        if ($restaurant = $this->restaurantRepository->find($id))
        {
            return $this->json([
                'data' => [
                    'dishes' => $this->formatDishCollection($restaurant->getValidDishes(),$locale)
                ]
            ],Response::HTTP_OK);
        }
        throw new BadRequestException();
    }

    #[Route('public/restaurant/{id}/info', name: 'restaurant_dishes',methods: ['GET'])]
    public function notValidDishes($id,Request $request) : Response
    {
        $locale = $request->headers->get('Locale','ru');
        if ($restaurant = $this->restaurantRepository->find($id))
        {
            return $this->json([
                'data' => [
                    'dishes' => $this->formatDishCollection($restaurant->getNotValidDishes(),$locale)
                ]
            ],Response::HTTP_OK);
        }
        throw new BadRequestException();
    }

    #[Route('public/restaurant/{id}', name: 'restaurant_get',methods: ['GET'])]
    public function getRestaurant($id,Request $request): Response
    {
        $fields = $this->getRequestedFields(
            request: $request,
            expectedFields: $this->restMeta->getScalarFieldsNames(),
            default: $this->restMeta->getDefaultScalarFieldsNames()
        );
        if ($restaurant = $this->restaurantRepository->find($id))
        {
            return $this->json([
                'data' => $this->restMeta->getScalarValuesByField($restaurant,$fields)
            ]);
        }
        throw new BadRequestException();
    }

    #[Route('kek/restaurant', name: 'restaurant_update',methods: ['GET'])]
    public function updateRestaurant(Request $request) : Response
    {

        if ($restaurant = $this->restaurantRepository->findAll())
        {
            return $this->json([
                'kek' => array_map(function ($kek){
                    return [$kek->getId()];
                },$restaurant)
            ]);
        }
        throw new BadRequestException();
    }

    #[Route('kek/dish', name: 'restaurant_update',methods: ['GET'])]
    public function lllll(Request $request) : Response
    {

        if ($restaurant = $this->rep->findAll())
        {
            return $this->json([
                'kek' => array_map(function ($kek){
                    return [$kek->getId()];
                },$restaurant)
            ]);
        }
        throw new BadRequestException();
    }
}
