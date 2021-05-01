<?php

namespace App\Configurators\Entity;

use App\Entity\Table;

class TableConfig extends BaseConfigurator
{

	protected function getEntity(): string
	{
		return Table::class;
	}
}