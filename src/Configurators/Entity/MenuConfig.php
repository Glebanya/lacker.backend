<?php

namespace App\Configurators\Entity;

use App\Api\Access;
use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Dish;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MenuConfig extends BaseConfigurator
{
	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected Serializer $serializer,
		protected ApiService $apiService,
		protected Access $access,
	)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return Menu::class;
	}

	public function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), [
				'add_dish' => function(Menu $object, array $params)
				{
					if (array_key_exists('dish',$params) && is_array($rawDish = $params['dish']))
					{
						$object->addDish($dish = new Dish($rawDish));
						if (count($errors = $this->validator->validate($dish, groups: "create")) === 0)
						{
							$this->manager->persist($dish);
							$this->manager->flush();
							return $this->serializer->serialize(
								$this->apiService->buildApiEntityObject($dish)
							);
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				},
			]
		);
	}
}