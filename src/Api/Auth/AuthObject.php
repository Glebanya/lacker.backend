<?php


namespace App\Api\Auth;

use Firebase\JWT\JWT;

class AuthObject {

    /**
     * @var string
     */
    private string $key;

    private array $params;

    public static function create(): AuthObject {
        return new static();
    }

    public function setKey(string $key) : self {
        $this->key = $key;
        return $this;
    }

    public static function decode(string $item) : array {
        return (array) JWT::jsonDecode(base64_decode($item));
    }

    public function validate() : bool {
        $result = false;
        try {
            if($this->key && !empty($key)) {
                $this->params = (array) JWT::decode($this->key, 'key', array_keys(JWT::$supported_algs));
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