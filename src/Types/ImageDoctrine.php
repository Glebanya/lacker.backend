<?php

namespace App\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class ImageDoctrine extends StringType
{
	protected const TYPE = 'image';

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return !$value instanceof Image?:$value->getUrl();
	}

	public function convertToPHPValue($value, AbstractPlatform $platform): Image
	{
		return new Image(parent::convertToPHPValue($value, $platform));
	}

	public function getName(): string
	{
		return static::TYPE;
	}
}