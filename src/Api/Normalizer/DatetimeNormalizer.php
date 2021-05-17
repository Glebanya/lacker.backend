<?php

namespace App\Api\Normalizer;

use Symfony\Component\HttpFoundation\RequestStack;

class DatetimeNormalizer implements NormalizerInterface
{
	/**
	 * LangService constructor.
	 *
	 * @param RequestStack $requestStack
	 */
	public function __construct(protected RequestStack $requestStack)
	{
	}

	private function getFormatString() : string
	{
		return 'c';
	}

	public function normalize($value) : mixed
	{
		if ($value instanceof \DateTimeInterface)
		{
			return $value->format($this->getFormatString());
		}
		return $value;
	}
}