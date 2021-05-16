<?php

namespace App\Configurators\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Field
{
	public function __construct(
		public string $name,
		public string $getter,
		public string|null $setter = null,
		public bool $immutable = false,
		public bool $default = false,
	)
	{
	}
}