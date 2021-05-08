<?php

namespace App\Api;

final class ApiEntity
{
	public function __construct(private object $object, private ConfiguratorInterface $resolver, private ApiService $service)
	{
	}

	public function getObject (): object
	{
		return $this->object;
	}
	public function setProperty(string $key,$value)
	{
		if ($this->resolver->getPropertyBuilderCollection()->has($key))
		{
			$this->resolver->getPropertyBuilderCollection()->get($key)?->build($this->object)?->set($value);
		}
		return null;
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function getProperty(string $key, array $params = []): mixed
	{
		if ($this->resolver->getPropertyBuilderCollection()->has($key))
		{
			return $this->resolver->getPropertyBuilderCollection()->get($key)->build($this->object)->value();
		}

		return null;
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return ApiEntity|ApiEntity[]|null
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

		return null;
	}

	/**
	 * @param string $key
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function method(string $key, array $params): mixed
	{
		if ($this->resolver->getMethodBuilderCollection()->has($key))
		{
			return $this->resolver->getMethodBuilderCollection()->get($key)->build($this->object)->execute($params);
		}

		return null;
	}
}