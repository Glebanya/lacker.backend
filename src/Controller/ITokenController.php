<?php


namespace App\Controller;


use App\Api\Event\TokenEventSubscriber;

interface ITokenController
{
    function setAccessController(TokenEventSubscriber $accessController);
    function getAccessController() : ?TokenEventSubscriber;
}