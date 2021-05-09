<?php


namespace App\Configurators\Entity;

use App\Entity\Table;
use App\Entity\TableReserve;
use App\Entity\User as UserEntity;
use App\Entity\Order;
use App\Entity\Portion;
use App\Entity\Restaurant;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserConfig extends BaseConfigurator
{
	public function __construct(protected EntityManagerInterface $manager, protected ValidatorInterface $validator,)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return UserEntity::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(
			parent::getMethodsList(),
			[
				'reserve_table' => function(UserEntity $user, array $params) {
					if (array_key_exists('table',$params) && is_string($params['table']))
					{
						if (
							($table = $this->manager->find(Table::class,$params['table'])) &&
							$table instanceof Table
						)
						{
							if (!$table->getCurrentReserve())
							{
								$reserve = (new TableReserve())
									->setUser($user)
									->setStatus(TableReserve::STATUS_NEW)
									->setReservedTable($table);
								if (count($errors = $this->validator->validate($reserve,groups: "create")) === 0
								)
								{
									$this->manager->persist($reserve);
									$this->manager->flush();
									return $reserve->getId();
								}
								throw new \Exception((string)$errors);
							}
							throw new \Exception("table reserved ");
						}
						throw new \Exception("unknown table");
					}
					throw new \Exception("wrong params");
				},
				'make_order' => function(UserEntity $user, array $params) {
					if (
						array_key_exists('portions',$params) && is_array($params['portions']) &&
						array_key_exists('restaurant',$params) && is_string($params['restaurant'])
					)
					{
						if (($restaurant = $this->manager->find(Restaurant::class,$params['restaurant'])) && $restaurant instanceof Restaurant)
						{
							$order = (new Order($params))
								->setUser($user)
								->setRestaurant($restaurant);
							$dishCollection = $this->manager->getRepository(Portion::class)
								->matching(
									Criteria::create()->where(
										Criteria::expr()->in('id',$params['portions'])
									)
								);
							foreach ($dishCollection as $dish)
							{
								$order->addPortion($dish);
							}
							if (count($errors = $this->validator->validate($order, groups: "create")) === 0)
							{
								$this->manager->flush();
								return $order->getId();
							}
							throw new \Exception((string)$errors);
						}
						throw new \Exception("unknown restaurant");
					}
					throw new \Exception("wrong params");
				},
			]);
	}
}