<?php


namespace App\Controller;


use App\Api\Event\ApiManager;

interface ITokenController
{
    public function setApiManager(ApiManager $apiManager);
    public function getApiManager() : ?ApiManager;
    public function getNonPublicMethods() : array;
}