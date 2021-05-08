<?php

namespace App\Configurators\Entity;

use App\Entity\Dish;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MenuConfig extends BaseConfigurator
{
	public function __construct(protected EntityManagerInterface $manager, protected ValidatorInterface $validator,)
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
				'remove_dish' => function(Menu $object, array $params) {
					if (array_key_exists('dish', $params) && is_string($params['dish']))
					{
						if (
							($dish = $this->manager->getPartialReference(Dish::class, $params['dish'])) &&
							$dish instanceof Dish
						)
						{
							$object->removeDish($dish);
							$this->manager->flush();
						}
					}

					return true;
				},
				'add_dish' => function(Menu $object, array $params) {
					if (array_key_exists('dish',$params) && is_array($params['dish']))
					{
						if (count($errors = $this->validator->validate($dish = new Dish($params['dish']))) === 0)
						{
							$object->addDish($dish);
							$this->manager->flush();
							return $dish->getId();
						}

					}
				},
			]
		);
	}
}