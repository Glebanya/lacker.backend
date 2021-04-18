<?php


namespace App\Api;


final class ApiEntity
{
    public function __construct(private object $object, private ConfiguratorInterface $resolver)
    {}

    /**
     * @param string $key
     * @param array $params
     * @return mixed
     */
    public function property(string $key,array $params = []) : mixed
    {
        if ($this->resolver->getPropertyBuilderCollection()->has($key))
        {
            return $this->resolver->getPropertyBuilderCollection()->get($key)->build($this->object)->value($params);
        }
        return null;
    }

    /**
     * @param string $key
     * @return ApiEntity|ApiEntity[]|null
     */
    public function reference(string $key) : ApiEntity|array|null
    {
        if ($this->resolver->getMethodBuilderCollection()->has($key))
        {
            if ($result = $collection->get($key)->getValue($this->object))
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
     * @return mixed
     */
    public function method(string $key,array $params) : mixed
    {
        if ($this->resolver->getMethodBuilderCollection()->has($key))
        {
            return $this->resolver->getMethodBuilderCollection()->get($key)->build($this->object)->execute($params);
        }
        return null;
    }
}