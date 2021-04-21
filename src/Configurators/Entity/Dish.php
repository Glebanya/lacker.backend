<?php


namespace App\Configurators\Entity;

use App\Api\ConfiguratorInterface;
use App\Entity\Dish as DishEntity;


class Dish extends BaseConfigurator  implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = DishEntity::class;

    protected function getMethods(): array
    {
        return [];
    }
}