<?php

namespace App\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class LangDoctrine extends JsonType
{
	protected const TYPE = 'lang_phrase';

	public function convertToPHPValue($value, AbstractPlatform $platform): Lang
	{
		return new Lang(parent::convertToPHPValue($value, $platform));
	}

	public function getName(): string
	{
		return static::TYPE;
	}
}