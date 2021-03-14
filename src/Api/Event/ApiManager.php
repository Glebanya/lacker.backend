<?php


namespace App\Api\Event;

use App\Api\Auth;
use App\Api\Request;
use App\Entity\Access;
use App\Entity\Business;
use App\Entity\Client;
use App\Entity\Stuff;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ITokenController;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Api\Auth\Access\AccessEnum;

class ApiManager implements EventSubscriberInterface
{


    private Request $request;

    public function __construct(
        Request $request,
        Auth\AuthHandlerObject $authObject
    ){
        $this->request = $request;
    }
    private function handleControllerEvent(ITokenController $controller, string $method){

    }
    public function onKernelController(ControllerEvent $event) {

        if (is_array($controller = $event->getController())) {
            [$controller,$method] = $controller;
        }

        if ($controller instanceof ITokenController) {

            if (in_array($method, array_keys($methods = $controller->getMethodMap()))){

                ['type' => $accessType,'rights' => $accessRights] = $methods[$method];
                if ($accessType === 'client'){
                    if (!$this->request->getUser() instanceof Client){
                        throw new AccessDeniedHttpException();
                    }
                } elseif ($accessType === 'stuff'){
                    $user = $this->request->getUser();
                    if (!$user instanceof Stuff){
                        throw new AccessDeniedHttpException();
                    } elseif ($user->getRole() & $accessRights === 0){
                        throw new AccessDeniedHttpException();
                    }
                }
            }
            $controller->setApiManager($this);
        }
    }

    public function getRequest() : ?Request {
        return $this->request;
    }

    #[ArrayShape([KernelEvents::CONTROLLER => "string"])]
    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}