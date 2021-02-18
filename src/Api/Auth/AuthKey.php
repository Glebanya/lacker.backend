<?php


namespace App\Api\Auth;


class AuthKey
{
    public static function getKey() {
        return $_ENV['APP_ENV_KEY'];
    }
}