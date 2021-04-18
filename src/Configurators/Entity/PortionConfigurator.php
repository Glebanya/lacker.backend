<?php


namespace App\Configurators\Entity;


use App\Api\ConfiguratorInterface;
use App\Entity\Portion;

final class PortionConfigurator extends BaseConfigurator  implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = Portion::class;

    protected function getMethods(): array
    {
        return [];
    }
}