<?php

namespace App\Configurators\Entity;

use App\Entity\SubOrder;

class SubOrderConfigurator extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return SubOrder::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), []);
	}
}