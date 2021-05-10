<?php

namespace App\Lang;

use App\Types\Lang;
use Symfony\Component\HttpFoundation\RequestStack;

class LangService
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
		return [self::RUSSIAN];
	}

	/**
	 * @param Lang|null $lang
	 *
	 * @return string
	 */
	public function formatLangObject(?Lang $lang): string
	{
		if ($lang instanceof Lang && array_key_exists($requestLang = $this->getRequestLanguage(),(array)$lang))
		{
			return $lang[$requestLang];
		}
		return '';
	}

	/**
	 * @return string
	 */
	protected function getRequestLanguage(): string
	{
		$request = $this->requestStack->getCurrentRequest();
		if (
			$request->headers->has('Locale') and
			in_array($lang = $request->headers->get('Locale'),$this::getSupportedLanguages())
		)
		{
			return $lang;
		}
		return $this->getDefaultLanguage();
	}

	/**
	 * @return string
	 */
	public function getDefaultLanguage(): string
	{
		return static::RUSSIAN;
	}
}