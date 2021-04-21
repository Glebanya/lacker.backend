<?php


namespace App\Configurators\Entity;


use App\Api\ConfiguratorInterface;
use App\Entity\Portion as PortionEntity;

class Portion extends BaseConfigurator  implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = PortionEntity::class;

    protected function getMethods(): array
    {
        return [];
    }
}