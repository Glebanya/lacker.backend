<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseCorsListener implements EventSubscriberInterface
{
	public function onCorsRequest(RequestEvent $event)
	{
		if ($event->getRequest()->getMethod() === 'OPTIONS')
		{
			$event->setResponse(new Response());
		}
	}

	public function onCorsResponse(ResponseEvent $event)
	{
		$event->getResponse()->headers->add([
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Headers' => 'X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization',
			'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS, PUT, DELETE',
			'Allow' => 'GET, POST, OPTIONS, PUT, DELETE',
		]);
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::RESPONSE => 'onCorsResponse',
			KernelEvents::REQUEST => ['onCorsRequest',9999]
		];
	}
}