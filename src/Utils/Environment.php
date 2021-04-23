<?php

namespace App\Utils;

class Environment
{
	public static function get(string $key)
	{
		if (array_key_exists($key, $_ENV))
		{
			return $_ENV[$key];
		}

		return null;
	}
}