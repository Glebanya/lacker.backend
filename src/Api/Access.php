<?php

namespace App\Api;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class Access
{
	public function __construct(private Security $security)
	{

	}

	public function denyAccessUnlessGranted(
		string $attribute,
		ApiEntity|ApiEntityCollection $entity,
		string $message = 'Access Denied'
	)
	{
		if (!$this->isGranted($attribute,$entity))
		{
			$exception = $this->createAccessDenyException($message);
			$exception->setAttributes($attribute);
			$exception->setSubject($entity);
			throw $exception;
		}
	}

	private function createAccessDenyException($message): AccessDeniedException
	{
		return new AccessDeniedException($message);
	}

	public function isGranted(string $attribute, ApiEntity|ApiEntityCollection $entity) : bool
	{
		return match ($entity instanceof ApiEntity){
				true => $this->isGrantedObject($attribute,$entity),
				false => $this->isGrantedCollection($attribute,$entity)
		};
	}

	private function isGrantedObject(string $attribute, ApiEntity $entity) : bool
	{
		return $this->security->isGranted($attribute,$entity->getObject());
	}

	private function isGrantedCollection(string $attribute, ApiEntityCollection $collection) : bool
	{
		foreach ($collection as $entity)
		{
			if (!$this->isGrantedObject($attribute,$entity))
			{
				return false;
			}
		}
		return true;
	}
}