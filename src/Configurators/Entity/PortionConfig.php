<?php


namespace App\Configurators\Entity;


use App\Api\ConfiguratorInterface;
use App\Entity\Portion as PortionEntity;

class PortionConfig extends BaseConfigurator  implements ConfiguratorInterface
{
	protected function getEntity(): string
	{
		return PortionEntity::class;
	}
}