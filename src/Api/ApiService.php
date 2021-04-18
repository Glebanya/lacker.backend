<?php


namespace App\Api;


use App\Api\Attributes\ConfiguratorAttribute;
use Psr\Container\ContainerInterface;

final class ApiService
{

    public function __construct(private ContainerInterface $container)
    {}

    public function buildApiEntityObject(object $object) : ApiEntity|null
    {
        if (!empty($attributes = (new \ReflectionClass($object))->getAttributes(ConfiguratorAttribute::class)))
        {
            if ($this->container->has($resolver = current($attributes)->newInstance()->entity))
            {
                return new ApiEntity($object, $this->container->get($resolver));
            }
        }
        return null;
    }
}