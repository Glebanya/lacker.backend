<?php


namespace App\Api\Event;

use App\Api\Auth;
use App\Api\Request;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ITokenController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Api\Auth\Access\AccessValidator;

class ApiManager implements EventSubscriberInterface
{

    private AccessValidator $validator;

    private EntityManagerInterface $manager;

    private ?Client $user;

    private Auth\AuthHandlerObject $authObject;

    private Request $request;

    private function validateAccessToken(string $token) : bool {

        return $this->authObject->setKey($token)->validate();
    }

    private function findUser(): ?object {
        $params = $this->authObject->getParams();
        return is_array($params) && array_key_exists('user_id', $params)?
            $this->manager->getRepository(Client::class)->find($params['user_id']) :
            null;
    }

    public function __construct(
        Request $request,
        EntityManagerInterface $entityManager,
        AccessValidator $validator,
        Auth\AuthHandlerObject $authObject
    ){
        $this->request = $request;
        $this->manager = $entityManager;
        $this->validator = $validator;
        $this->authObject = $authObject;
    }

    public function onKernelController(ControllerEvent $event) {

        if (is_array($controller = $event->getController())) {
            [$controller,$method] = $controller;
        }

        if ($controller instanceof ITokenController) {
            if (in_array($method, $controller->getNonPublicMethods())){
                if (!$event->getRequest()->request->has('access_token') && !$this->validateAccessToken($this->request->getAccessToken())){
                    throw new AccessDeniedHttpException('This action needs a valid token!');
                } elseif (!$this->user = $this->findUser()) {
                    throw new AccessDeniedHttpException('This user not exists!');
                }
            }
            $controller->setApiManager($this);
        }

    }

    public function getUser() : ?Client {
        return $this->user;
    }

    public function getRequest() : ?Request {
        return $this->request;
    }

    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}