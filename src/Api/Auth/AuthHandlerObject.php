<?php


namespace App\Api\Auth;

use Firebase\JWT\JWT;

class AuthHandlerObject {

    /**
     * @var string $key
     */
    private string $key;
    /**
     * @var array $params
     */
    private array $params;

    public static function create(): AuthHandlerObject {
        return new static();
    }

    public function setKey(string $key) : self {
        $this->key = $key;
        return $this;
    }

    public function validate() : bool {
        $result = false;
        try {
            if($this->key && !empty($this->key)) {
                $this->params = (array) JWT::decode($this->key,AuthKey::getKey(), array_keys(JWT::$supported_algs));
                $result = true;
            }
        } catch (\Throwable $exception) {
            $result = false;
        } finally {
            return $result;
        }
    }

    public function getParams() : array {
        return $this->params;
    }
}