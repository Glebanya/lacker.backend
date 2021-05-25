<?php


namespace App\Configurators\Entity;

use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Configurators\Exception\EntityNotFoundException;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Restaurant;
use App\Entity\Table;
use App\Entity\TableReserve;
use App\Entity\User;
use App\Entity\Order;
use App\Repository\PortionRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserConfig extends BaseConfigurator
{
	public function __construct(
		protected RestaurantRepository $restaurantRepository,
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected PortionRepository $portionRepository,
		protected ApiService $apiService,
		protected Serializer $serializer
	)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return User::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), [
				'reserve_table' => function(User $user, array $params)
				{
					if (array_key_exists('table',$params) && is_string($tableId = $params['table']))
					{
						if ($table = $this->manager->find(Table::class,$tableId))
						{
							if (!$table->getCurrentReserve())
							{
								if (count($errors = $this->validator->validate($reserve = new TableReserve($user,$table), groups: "create")) === 0)
								{
									$this->manager->persist($reserve);
									$this->manager->flush();
									return $this->serializer->serialize(
										$this->apiService->buildApiEntityObject($reserve)
									);
								}
								throw new ValidationException($errors);
							}
							throw new \Exception("table reserved");
						}
						throw new EntityNotFoundException($tableId);
					}
					throw new ParameterException("wrong params");
				},
				'make_order' => function(User $user, array $params)
				{
					if (
						array_key_exists('table',$params) && is_string($tableId =  $params['table']) and
						array_key_exists('sub_order',$params) && is_array($suborder = $params['sub_order'])
					)
					{
						if ($table = $this->manager->find(Table::class,$tableId))
						{
							if (count($errors = $this->validator->validate($order = new Order($table,$user), groups: "create")) === 0)
							{
								$this->manager->persist($order);
								return $this->apiService
									->buildApiEntityObject($order)
									->method('add_suborder', $suborder);
							}
							throw new ValidationException($errors);
						}
						throw new EntityNotFoundException($tableId);
					}
					throw new ParameterException("wrong params");
				},
			]);
	}
}