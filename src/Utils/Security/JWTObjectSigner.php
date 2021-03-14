<?php


namespace App\Utils\Security;

use App\Utils\Environment;
use Firebase\JWT\JWT;

class JWTObjectSigner
{

    public function __construct(private array $params)
    {}

    public function sign($algo = 'HS256') : string
    {
        return JWT::encode(
            $this->params,
            Environment::get('APP_ENV_KEY'),
            $algo
        );
    }
}