<?php

namespace App\Access;

use App\Entity\Order;

class OrderAccessVoter extends AbstractAccessVoter
{

	protected function getAttributes(): array
	{
		return [];
	}

	protected function getEntity(): string
	{
		return Order::class;
	}
}