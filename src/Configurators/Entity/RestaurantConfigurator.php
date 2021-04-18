<?php


namespace App\Configurators\Entity;

use App\Entity\Restaurant,
    App\Api\ConfiguratorInterface;



final class RestaurantConfigurator extends BaseConfigurator implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = Restaurant::class;

    protected function getMethods(): array
    {
        return [];
    }
}