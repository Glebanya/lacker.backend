<?php

namespace App\Types;

use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class PriceDoctrine extends JsonType
{
	protected const TYPE = 'price';

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new Price(parent::convertToPHPValue($value, $platform));
	}

	public function getName(): string
	{
		return static::TYPE;
	}
}