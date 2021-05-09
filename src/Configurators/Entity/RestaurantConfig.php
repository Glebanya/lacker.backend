<?php

namespace App\Configurators\Entity;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Entity\Restaurant as RestaurantEntity, App\Api\ConfiguratorInterface;
use App\Entity\Staff;
use App\Entity\User;
use App\Entity\User as UserEntity;
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
		return array_merge_recursive(
			parent::getMethodsList(),[
			'add_menu' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('menu',$params) && is_array($params['menu']))
				{
					if (count($errors = $this->validator->validate($staff = new Menu($params['menu']),groups: "create")) === 0)
					{
						$object->addMenu($staff);
						$this->manager->flush();
						return $object->getId();
					}
					throw new \Exception((string)$errors);
				}
				throw new \Exception("wrong params");
			},
			'add_staff' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('staff',$params) && is_array($params['staff']))
				{
					if (count($errors = $this->validator->validate($staff = new Staff($params['staff']), groups: "create")) === 0)
					{
						$object->addStaff($staff);
						$this->manager->flush();
					}
					throw new \Exception((string)$errors);
				}
				throw new \Exception("wrong params");
			},
			'make_order' => function(Restaurant $object, array $params) {
				if (array_key_exists('portions', $params) && is_array($params['portions']))
				{
					if (($user = $this->security->getToken()->getUser()) && $user instanceof User)
					{
						if (!$user->getCurrentOrder())
						{
							$order = (new Order($params))->setUser($user)->setRestaurant($object);
							$portionCollection = $this->manager->getRepository(Portion::class)
								->matching(
									Criteria::create()->where(
										Criteria::expr()->in('id',$params['portions'])
									)
								);
							foreach ($portionCollection as $portion)
							{
								$order->addPortion($portion);
							}
							if (count($errors = $this->validator->validate($order,groups: "create")) === 0)
							{
								$this->manager->flush();
								return $order->getId();
							}
							throw new \Exception((string)$errors);
						}
						throw new \Exception("user has unpaid orders");
					}
					throw new \Exception("unknown user");
				}
				throw new \Exception("wrong params");
			},
		]);
	}
}