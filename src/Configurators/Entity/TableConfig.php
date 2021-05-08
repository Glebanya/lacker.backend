<?php

namespace App\Configurators\Entity;

use App\Entity\Table;

class TableConfig extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return Table::class;
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