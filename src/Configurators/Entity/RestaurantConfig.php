<?php

namespace App\Configurators\Entity;

use App\Api\Access;
use App\Api\ApiEntityCollection;
use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\Restaurant as RestaurantEntity, App\Api\ConfiguratorInterface;
use App\Entity\Staff;
use App\Entity\SubOrder;
use App\Repository\SubOrderRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestaurantConfig extends BaseConfigurator implements ConfiguratorInterface
{
	protected const ENTITY_CLASS = RestaurantEntity::class;

	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected Security $security,
		protected Serializer $serializer,
		protected ApiService $apiService,
		protected Access $access,
		protected SubOrderRepository $subOrderRepository
	)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return RestaurantEntity::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), [
			'add_menu' => function(RestaurantEntity $object, array $params)
			{
				if (array_key_exists('menu',$params) && is_array($params['menu']))
				{
					$object->addMenu($menu = new Menu($params['menu']));
					if (0 === count($errors = $this->validator->validate($menu, groups: "create")))
					{
						$this->manager->persist($menu);
						$this->manager->flush();
						return $this->serializer->serialize(
							$this->apiService->buildApiEntityObject($menu)
						);
					}
					throw new ValidationException($errors);
				}
				throw new ParameterException("wrong params");
			},
			'add_staff' => function(RestaurantEntity $object, array $params)
			{
				if (array_key_exists('staff',$params) && is_array($rawStaff = $params['staff']))
				{
					$staff = new Staff($rawStaff['staff']);
					if (count($errors = $this->validator->validate($staff, groups: "create")) === 0)
					{
						$object->addStaff($staff);
						$this->manager->persist($staff);
						$this->manager->flush();
						return $this->serializer->serialize(
							$this->apiService->buildApiEntityObject($staff)
						);
					}
					throw new ValidationException($errors);
				}
				throw new ParameterException("wrong params");
			},
			'suborders' => function(RestaurantEntity $restaurant, array $params)
			{
				if (array_key_exists('checked',$params) && is_bool($params['checked']))
				{
					return $this->serializer->serialize(
						array_reduce(
							array_merge_recursive(...$restaurant->getOrders()->matching(
								Criteria::create()
									->where(Criteria::expr()->eq('checked',$params['checked']))
									->andWhere(Criteria::expr()->eq('deleted', false))
							)->map(
								fn (Order $order) =>
									$order->getSubOrders()->matching(
										Criteria::create()
											->andWhere(
												Criteria::expr()->eq('checked', false)
											)
											->orderBy([
												'updateDate' => Criteria::DESC
											])
									)->toArray()
							)->toArray()),
							fn (ApiEntityCollection $collection ,SubOrder $subOrder) => $collection->addEntity(
								$this->apiService->buildApiEntityObject($subOrder)
							),
							new ApiEntityCollection()
						)
					);
				}
				throw new ParameterException("wrong params");
			}
		]);
	}
}