<?php

namespace App\Api\Normalizer;

use Symfony\Component\Uid\AbstractUid;

class UuidNormalizer implements NormalizerInterface
{

	public function normalize($value): mixed
	{
		if ($value instanceof AbstractUid)
		{
			return $value->jsonSerialize();
		}

		return $value;
	}
}