<?php

namespace App\Configurators\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Reference
{
	public function __construct(public string $name)
	{
	}
}