<?php


namespace App\Api\Auth\Signup;


class SignUpFactory
{
    public static function create(string $type) : ISignUp
    {
        return new Service\PureSignUpObject;
        /*switch ($type)
        {
            case SignUpType::APPLE_AUTH_TYPE;
            case SignUpType::APPLE_AUTH_TYPE;
            case SignUpType::APPLE_AUTH_TYPE;

            default;
        }*/
    }
}