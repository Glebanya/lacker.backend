<?php


namespace App\Api\Auth;

use Firebase\JWT\JWT;

class AuthSignerObject {
    /**
     * @var string $key
     */
    private string $key;
    /**
     * @var array $params
     */
    private array $params;

    public static function create(): AuthSignerObject {
        return new static();
    }

    public function setParams(array $params) : AuthSignerObject {
        $this->params = $params;
        return $this;
    }
    public function sign($algo = 'HS256') : string {
        return JWT::encode($this->params,AuthKey::getKey(),$algo);
    }
}