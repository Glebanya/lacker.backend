<?php


namespace App\Api;

use App\Api\Auth\AuthHandlerObject;
use App\Entity\Business;
use App\Entity\Client;
use App\Entity\Stuff;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    private ?SymfonyRequest $request;

    private ?array $content;

    private Client|Stuff|null $user;

    private ?Business $business;

    private function decodeContent() : array {
        if (!empty($content = $this->request->getContent(false) )) {
            try {
                return json_decode($content,true);
            } catch (\Throwable $exception){}
        }
        return [];
    }
    private function getEntityClass($type) : string {
        return $type === 'stuff'? Stuff::class : Client::class;
    }
    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $manager,
        AuthHandlerObject $authObject
    ) {

        $this->request = $requestStack->getCurrentRequest();
        $this->content = $this->decodeContent();

        if ($authObject->setKey($this->getAccessToken()) && $authObject->validate() && $userId = $authObject->getUserId()){
            $this->user = $manager->getRepository($this->getEntityClass($authObject->getType()))->find($userId);
        }
        if ($businessId = $this->getBusinessId()){
            $this->business = $manager->getRepository(Business::class)->find($businessId);
        }

    }

    public function getContent() : ?array {
        return $this->content;
    }

    public function getRequest() : ?SymfonyRequest {
        return $this->request;
    }

    public function getAccessToken() : ?string {
        if ($this->content && array_key_exists('access_token',$this->content) && is_string($token = $this->content['access_token'])) {
            return $token ;
        }
        return $this->request->request->get('access_token');
    }

    public function getBusinessId() : ?string {
        if ($this->content && array_key_exists('business_id',$this->content) && is_string($id = $this->content['business_id'])) {
            return $id;
        }
        return $this->request->request->get('business_id');
    }

    public function getUser(): Client|Stuff|null {
        return $this->user;
    }

    public function getBusiness(): Business|null {
        return $this->business;
    }
}
