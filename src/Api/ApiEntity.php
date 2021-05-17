<?php

namespace App\Api;

use Doctrine\Common\Collections\Collection;
use App\Entity\BaseObject;
use App\Types\Lang;
use Exception;

final class ApiEntity
{
	public function __construct(private object $object, private ConfiguratorInterface $resolver, private ApiService $service)
	{
	}

	public function getObject (): object
	{
		return $this->object;
	}

	public function getFullFieldsNames() : array
	{
		return $this->resolver->getPropertyBuilderCollection()->getFullNames();
	}

	public function getDefaultFieldsNames() : array
	{
		return $this->resolver->getPropertyBuilderCollection()->getDefaults();
	}

	/**
	 * @param string $key
	 * @param $value
	 *
	 * @throws Exception
	 */
	public function setProperty(string $key,$value)
	{
		if ($this->resolver->getPropertyBuilderCollection()->has($key))
		{
			$this->resolver->getPropertyBuilderCollection()->get($key)?->build($this->object)?->set($value);
		}
		throw new Exception("unknown field $key");
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function getProperty(string $key): mixed
	{
		if ($this->resolver->getPropertyBuilderCollection()->has($key))
		{
			$value = $this->resolver->getPropertyBuilderCollection()->get($key)->build($this->object)->value();
			if ($value instanceof Collection)
			{
				return array_reduce(
					iterator_to_array($value),
					function(ApiEntityCollection $collection, $entity){
						return $collection->addEntity($this->service->buildApiEntityObject($entity));
					},
					new ApiEntityCollection()
				);
			}
			if ($value instanceof BaseObject)
			{
				$value = $this->service->buildApiEntityObject($value);
			}
			return $value;
		}

		throw new Exception("unknown field $key");
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return ApiEntity|ApiEntityCollection
	 * @throws Exception
	 */
	public function reference(string $key, array $params): ApiEntity|ApiEntityCollection
	{
		if ($this->resolver->getReferenceBuilderCollection()->has($key))
		{
			if ($result = $this->resolver->getReferenceBuilderCollection()->get($key)->build($this->object)->value($params))
			{
				if (is_iterable($result))
				{
					return array_reduce(
						$result instanceof \Traversable ? iterator_to_array($result) : $result,
						function(ApiEntityCollection $collection, $entity){
							return $collection->addEntity($this->service->buildApiEntityObject($entity));
						},
						new ApiEntityCollection()
					);
				}
				return $this->service->buildApiEntityObject($result);
			}
		}

		throw new Exception("unknown reference $key");
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function method(string $key, array $params): mixed
	{
		if ($this->resolver->getMethodBuilderCollection()->has($key))
		{
			return $this->resolver->getMethodBuilderCollection()->get($key)->build($this->object)->execute($params);
		}

		throw new Exception("unknown method $key");
	}
}