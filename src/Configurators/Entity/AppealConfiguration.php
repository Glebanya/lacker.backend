<?php

namespace App\Configurators\Entity;

use App\Entity\Appeal;

class AppealConfiguration extends BaseConfigurator
{
	protected function getEntity(): string
	{
		return Appeal::class;
	}
}