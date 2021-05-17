<?php

namespace App\Api\Normalizer;

use App\Types\Lang;
use Symfony\Component\HttpFoundation\RequestStack;

class LangNormalizer implements NormalizerInterface
{
	public const RUSSIAN = 'ru';

	/**
	 * LangService constructor.
	 *
	 * @param RequestStack $requestStack
	 */
	public function __construct(protected RequestStack $requestStack)
	{}

	/**
	 * @return string[]
	 */
	public static function getSupportedLanguages(): array
	{
		return [
			self::RUSSIAN
		];
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function normalize($value): mixed
	{
		if ($value instanceof Lang)
		{
			return array_key_exists($requestLang = $this->getRequestLanguage(),(array)$value)? $value[$requestLang] : '';
		}
		return $value;
	}

	/**
	 * @return string
	 */
	protected function getRequestLanguage(): string
	{
		$lang = $this->requestStack->getCurrentRequest()->headers->get('Locale',$this->getDefaultLanguage());
		return in_array($lang,$this::getSupportedLanguages())? $lang : $this->getDefaultLanguage();
	}

	/**
	 * @return string
	 */
	public function getDefaultLanguage(): string
	{
		return static::RUSSIAN;
	}
}