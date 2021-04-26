<?php

namespace App\Events;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionHandler implements EventSubscriberInterface
{

	#[ArrayShape([KernelEvents::EXCEPTION => "string"])]
	public static function getSubscribedEvents(): array
	{

		return [
			KernelEvents::EXCEPTION => 'onKernelException'
		];
	}

	public function onKernelException(ExceptionEvent $event)
	{

		$exception = $event->getThrowable();
		$message = sprintf('My Error says: %s with code: %s', $exception->getMessage(), $exception->getCode());
		$response = new Response();
		$response->setContent($message);

		if ($exception instanceof HttpExceptionInterface)
		{
			$response->setStatusCode($exception->getStatusCode());
			$response->headers->replace($exception->getHeaders());
		}
		else
		{
			$response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		// sends the modified response object to the event
		$event->setResponse($response);
	}
}