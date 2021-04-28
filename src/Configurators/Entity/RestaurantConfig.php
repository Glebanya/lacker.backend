<?php

namespace App\Configurators\Entity;

use App\Entity\Dish;
use App\Entity\Restaurant as RestaurantEntity, App\Api\ConfiguratorInterface;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestaurantConfig extends BaseConfigurator implements ConfiguratorInterface
{
	protected const ENTITY_CLASS = RestaurantEntity::class;

	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected MailerInterface $mailer
	)
	{
		parent::__construct();
	}

	protected function getMethodsList(): array
	{
		return [
			'remove_dish' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('dish', $params))
				{
					if ($dish = $this->manager->getPartialReference(Dish::class, $params['dish']))
					{
						$object->removeDish($dish);
						$this->manager->flush();
					}
				}

				return true;
			},
			'add_dish' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('dish',$params) && is_array($params['dish']))
				{
					if (count($errors = $this->validator->validate($dish = new Dish($params['dish']))) === 0)
					{
						$object->addDish($dish);
						$this->manager->flush();
					}
				}
			},
			'add_dish_batch' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('dishes',$params) && is_array($params['dish']))
				{
					foreach ($params['dish'] as $rawDish)
					{
						if (is_array($rawDish))
						{
							if (count($errors = $this->validator->validate($dish = new Dish($rawDish['dish']))) === 0)
							{
								$object->addDish($dish);
							}
						}
					}
					$this->manager->flush();
				}
			},
			'add_staff' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('staff',$params) && is_array($params['staff']))
				{
					if (count($errors = $this->validator->validate($staff = new Staff($params['staff']))) === 0)
					{
						$object->addStaff($staff);
						$this->manager->flush();
					}
				}
			},
			'remove_staff' => function(RestaurantEntity $object, array $params) {
				if (array_key_exists('staff',$params))
				{
					if ($staff = $this->manager->getPartialReference(Staff::class, $params['staff']))
					{
						$object->removeStaff($staff);
					}
				}
			},

		];
	}

	protected function getEntity(): string
	{
		return RestaurantEntity::class;
	}
}