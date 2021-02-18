<?php


namespace App\Api\Event;

use App\Api\Auth;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ITokenController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class TokenEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $manager;

    private $user;

    public function __construct(EntityManagerInterface $entityManager){
        $this->manager = $entityManager;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (is_array($controller = $event->getController())) {
            $controller = $controller[0];
        }

        if ($controller instanceof ITokenController){

            $token = $event->getRequest()->query->get('access_token');
            $authObject = Auth\AuthHandlerObject::create();
            if (!$token || !$authObject->setKey($token)->validate() && array_key_exists('user_id',$authObject->getParams() ?? [])) {

                throw new AccessDeniedHttpException('This action needs a valid token!');

            } elseif (!($this->user = $this->manager->getRepository(Client::class)->find($authObject->getParams()['user_id']))) {
                throw new AccessDeniedHttpException('This user not exists!');
            }
            $controller->setAccessController($this);
        }
    }

    public function getUser(){
        return $this->user;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}