<?php

namespace App\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ImageDoctrine extends StringType
{
	protected const TYPE = 'image';

	public function convertToDatabaseValue($value, AbstractPlatform $platform) : null|string
	{
		return !$value instanceof Image? null : $value->getUrl();
	}

	public function convertToPHPValue($value, AbstractPlatform $platform): ?Image
	{
		return $value = parent::convertToPHPValue($value, $platform)? new Image($value) : null;
	}

	public function getName(): string
	{
		return static::TYPE;
	}
}