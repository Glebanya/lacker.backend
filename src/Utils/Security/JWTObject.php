<?php


namespace App\Utils\Security;

use App\Utils\Environment;
use Firebase\JWT\JWT;

class JWTObject
{
    private ?array $params;

    /**
     * JWTObject constructor.
     * @param string $token
     */
    public function __construct(private string $token)
    {
        try
        {
            if ($this->token <> '')
            {
                $params = (array)JWT::decode(
                    $this->token,
                    Environment::get('APP_ENV_KEY'),
                    array_keys(JWT::$supported_algs)
                );
            }
        }
        finally
        {
            $this->params = $params ?? [];
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getParams(): array
    {
        return $this->params ?? [];
    }

    public function getUserId(): ?string
    {
        if ($this->params && array_key_exists('user_id', $this->params) && is_string($this->params['user_id']))
        {
            return $this->params['user_id'];
        }
        return null;
    }

    public function getType(): ?string
    {
        if ($this->params && array_key_exists('type', $this->params) && is_string($this->params['type']))
        {
            return $this->params['type'];
        }
        return null;
    }
}