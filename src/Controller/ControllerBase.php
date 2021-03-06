<?php


namespace App\Controller;


use App\Api\Event\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class ControllerBase extends AbstractController implements ITokenController
{
    private ApiManager $apiManager;

    public function setApiManager(ApiManager $apiManager) {
        $this->apiManager = $apiManager;
    }

    public function getApiManager(): ?ApiManager {
        return $this->apiManager;
    }

    public function getNonPublicMethods() : array {
        return [
            'update',
            'delete',
            'add'
        ];
    }
}