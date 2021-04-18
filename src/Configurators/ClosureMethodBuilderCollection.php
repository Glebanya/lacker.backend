<?php


namespace App\Configurators;


use App\Api\Builders\MethodBuilderInterface;
use App\Api\Collections\MethodBuilderCollectionInterface;
use App\Api\Properties\MethodInterface;

class ClosureMethodBuilderCollection implements MethodBuilderCollectionInterface
{
    protected \ArrayObject $array;

    public function __construct(array $methods)
    {
        $this->array = new \ArrayObject(array_filter(
            $methods,
            function ($item) : bool {
                return is_callable($item);
            }
        ));
    }

    public function has(string $property): bool
    {
        return $this->array->offsetExists($property);
    }

    public function get(string $property): MethodBuilderInterface|null
    {
        if ($this->has($property))
        {
            return new class($this->array->offsetGet($property)) implements MethodBuilderInterface {

                public function __construct(protected $callable)
                {}
                public function build(object $object): MethodInterface
                {
                    return new class($this->callable,$object) implements MethodInterface {
                        public function __construct(
                            protected $callable,
                            protected object $object
                        )
                        {}
                        public function execute(array $parameters): mixed
                        {
                            return call_user_func($this->callable,$this->object,$parameters);
                        }
                    };
                }
            };
        }
    }
}