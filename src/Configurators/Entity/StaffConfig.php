<?php


namespace App\Configurators\Entity;


use App\Entity\Staff;

class StaffConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return Staff::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(
			parent::getMethodsList(),
			[

			]
		);
	}
}