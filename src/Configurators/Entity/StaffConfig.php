<?php


namespace App\Configurators\Entity;

use App\Configurators\Entity\StaffConfig as StaffEntity;

class StaffConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return StaffEntity::class;
	}

	protected function getMethodsList(): array
	{
		// TODO: Implement getMethodsList() method.
	}
}