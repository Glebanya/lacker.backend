<?php


namespace App\Api\Auth\Security;

use App\Api\Auth\AuthKey;
use App\Entity\Client;

class PasswordHandler
{
    public static function hashPassword(string $password) : string {
        return hash('sha256',$password.AuthKey::getKey());
    }

    public static function validate(Client $user, string $password) : string {
        return $user->getPassword() === static::hashPassword($password);
    }
}