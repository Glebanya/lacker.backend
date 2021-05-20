<?php

namespace App\Configurators\Entity;

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
		return array_merge_recursive(
			parent::getMethodsList(),
			[
				'add_dish' => function(Menu $object, array $params)
				{
					if (array_key_exists('dish',$params) && is_array($rawDish = $params['dish']))
					{
						if (count($errors = $this->validator->validate($dish = new Dish($rawDish), groups: "create")) === 0)
						{
							$object->addDish($dish);
							$this->manager->persist($dish);
							$this->manager->flush();
							return $dish->getId();
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				},
			]
		);
	}
}