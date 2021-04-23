<?php


namespace App\Configurators\Entity;

use App\Configurators\Entity\MenuConfig as MenuEntity;

class MenuConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return MenuEntity::class;
	}
}