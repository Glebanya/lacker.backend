<?php

namespace App\Events;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		$response = new JsonResponse([
			'error' => [
				'message' => $exception->getMessage(),
				'code' => $exception->getCode(),
				'trace' => $exception->getTraceAsString()
			]
		]);
		if ($exception instanceof HttpExceptionInterface)
		{
			$response->setStatusCode($exception->getStatusCode());
			$response->headers->replace($exception->getHeaders());
		}
		else
		{
			$response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
		}

		$event->setResponse($response);
	}
}