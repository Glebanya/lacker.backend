<?php

namespace App\Api;

use App\Api\Attributes\ConfiguratorAttribute;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ApiService
{

	public function __construct(private ContainerInterface $container)
	{
	}

	public function buildApiEntityObject(object $object): ApiEntity|null
	{
		$reflection = new ReflectionClass($object);
		if (
			!empty($attributes = $reflection->getAttributes(ConfiguratorAttribute::class)) ||
			!empty($attributes = $reflection->getParentClass()?->getAttributes(ConfiguratorAttribute::class))
		)
		{
			if ($this->container->get($configurator = current($attributes)->newInstance()->entity))
			{
				return new ApiEntity($object, $this->container->get($configurator), $this);
			}
		}

		return null;
	}
}