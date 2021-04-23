<?php


namespace App\Configurators\Entity;

use App\Configurators\Entity\UserConfig as UserEntity;

class UserConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return UserEntity::class;
	}
}