<?php


namespace App\Api;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    private ?SymfonyRequest $request;

    private ?array $content;

    private function decodeContent() : array {
        if (!empty($content = $this->request->getContent(false) )) {
            try {
                return json_decode($content,true);
            } catch (\Throwable $exception){}
        }
        return [];
    }

    public function __construct(RequestStack $requestStack) {
        $this->request = $requestStack->getCurrentRequest();
        $this->content = $this->decodeContent();
    }

    public function getContent() : ?array {
        return $this->content;
    }

    public function getRequest() : ?SymfonyRequest {
        return $this->request;
    }

    public function getAccessToken() : ?string {
        if ($this->content && array_key_exists('access_token',$this->content) && is_string($token = $this->content['access_token'])) {
            return $token;
        }
        return null;
    }
}
