<?php

namespace App\Api;

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

	/**
	 * @return string[]
	 */
	public function getPropertiesNames() : array
	{
		return $this->resolver->getPropertyBuilderCollection()->getNames();
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
			return $this->resolver->getPropertyBuilderCollection()->get($key)->build($this->object)->value();
		}

		throw new Exception("unknown field $key");
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return ApiEntity|ApiEntity[]|null
	 * @throws Exception
	 */
	public function reference(string $key, array $params): ApiEntity|array|null
	{
		if ($this->resolver->getReferenceBuilderCollection()->has($key))
		{
			if ($result = $this->resolver->getReferenceBuilderCollection()->get($key)->build($this->object)->value($params))
			{
				if (is_iterable($result))
				{
					$entities = [];
					foreach ($result as $object)
					{
						$entities[] = $this->service->buildApiEntityObject($object);
					}

					return $entities;
				}

				return $this->service->buildApiEntityObject($result);
			}
		}

		throw new Exception("unknown field $key");
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