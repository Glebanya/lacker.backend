<?php


namespace App\Api\Auth;

use Firebase\JWT\JWT;

class AuthHandlerObject {

    /**
     * @var ?string $key
     */
    private ?string $key;
    /**
     * @var ?array $params
     */
    private ?array $params;

    public function setKey(?string $key) : self {
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
    public function getType(): ?string {
        if ($this->params && array_key_exists('type',$this->params) && is_string($this->params['type'])){
            return $this->params['type'];
        }
        return null;
    }
    public function getUserId() : ?string {
        if ($this->params && array_key_exists('user_id',$this->params) && is_string($this->params['user_id'])){
            return $this->params['user_id'];
        }
        return null;
    }
}