<?php


namespace App\Configurators\Entity;

use App\Api\ConfiguratorInterface;
use App\Entity\Dish;


final class DishConfigurator extends BaseConfigurator  implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = Dish::class;

    protected function getMethods(): array
    {
        return [];
    }
}