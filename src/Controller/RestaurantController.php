<?php

namespace App\Controller;

use App\Api\Access;
use App\Api\ApiEntity;
use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
	use ApiTrait;

	public function __construct(protected RestaurantRepository $restaurantRepository, protected ApiService $service)
	{}

	protected function getRestaurants(int $offset, int $limit): Collection
	{
		return $this->restaurantRepository->matching(
			Criteria::create()->where(
				Criteria::expr()->eq('deleted',false)
			)
			->setFirstResult($offset)
			->setMaxResults($limit)
		)->map(function($item) : ApiEntity {
			return $this->service->buildApiEntityObject($item);
		});
	}

	#[Route('/restaurants', name: 'get_restaurants',methods: ['GET'])]
	public function index(Request $request, Serializer $serializer, Access $access): Response
	{
		return $this->json([
			'data' => array_map(
				function(ApiEntity $entity) use ($access,$serializer){

					$access->denyAccessUnlessGranted('view',$entity);
					return $serializer->serialize($entity);
				},
				$this->getRestaurants(
					$request->query->getInt('offset'),
					$request->query->getInt('limit',50)
				)->toArray()
			)
	]);
	}
}
