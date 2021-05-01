<?php

namespace App\Configurators\Entity;

use App\Entity\Order;

class OrderConfig extends BaseConfigurator
{

	protected function getEntity(): string
	{
		return Order::class;
	}
}