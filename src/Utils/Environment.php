<?php


namespace App\Utils;


use JetBrains\PhpStorm\Pure;

class Environment
{
    #[Pure] public static function get(string $key)
    {
        if (array_key_exists($key,$_ENV))
        {
            return $_ENV[$key];
        }
        return null;
    }
}