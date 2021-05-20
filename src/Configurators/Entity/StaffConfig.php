<?php


namespace App\Configurators\Entity;


use App\Entity\Staff;

class StaffConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return Staff::class;
	}
}