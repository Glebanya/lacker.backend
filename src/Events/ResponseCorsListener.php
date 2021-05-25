<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseCorsListener implements EventSubscriberInterface
{
	public function __construct()
	{

	}

	public function onCorsResponse(ResponseEvent $event)
	{
		$event->getResponse()->headers->add([
			'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Headers' => ['X-API-KEY', 'Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Access-Control-Request-Method'],
			'Access-Control-Allow-Methods' => [ 'GET', 'POST', 'PUT', 'DELETE'],
			'Allow' => [ 'GET', 'POST', 'PUT', 'DELETE'],
		]);
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::RESPONSE => 'onCorsResponse'
		];
	}
}