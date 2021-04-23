<?php


namespace App\Configurators\Entity;

use App\Api\ConfiguratorInterface;
use App\Entity\Dish as DishEntity;


class DishConfig extends BaseConfigurator  implements ConfiguratorInterface
{
	protected function getEntity(): string
	{
		return DishEntity::class;
	}
}