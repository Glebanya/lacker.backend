<?php


namespace App\Configurators;


use App\Api\Builders\ReferenceBuilderInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;
use App\Api\Properties\ReferenceInterface;
use App\Configurators\Attributes\Collection;
use App\Configurators\Attributes\Reference;

class AnnotationReferenceBuilderCollection implements ReferenceBuilderCollectionInterface
{
    protected \ArrayObject $array;

    protected function getProperties() : array
    {
        $result = [];
        foreach ((new \ReflectionClass($this->entity))->getProperties() as $property)
        {
            if (!empty($values = $property->getAttributes(Reference::class)))
            {
                $field = current($values)->newInstance()->name;
                $result[$field] = $property;
            }
        }
        return $result;
    }

    public function __construct(
        protected string $entity
    )
    {
        $this->array = new \ArrayObject($this->getProperties());
    }


    public function has(string $property): bool
    {
        return $this->array->offsetExists($property);
    }

    public function get(string $property): ReferenceBuilderInterface|null
    {
        if ($this->has($property))
        {
            return new class ($this->array->offsetGet($property)) implements ReferenceBuilderInterface
            {
                public function __construct(private \ReflectionProperty $property)
                {}
                public function build(object $object): ReferenceInterface
                {
                    return new class($object,$this->property) implements ReferenceInterface {

                        public function __construct(private object $object, private \ReflectionProperty $property)
                        {}
                        public function value(int $offset = 0, int $limit = 1000): object|array
                        {
                            $this->property->setAccessible(true);
                            $value = $this->property->getValue($this->object);
                            if (!empty($property = $this->property->getAttributes(Collection::class)))
                            {
                                if ($value instanceof \Doctrine\Common\Collections\Collection)
                                {
                                    return $value->slice($offset,$limit);
                                }
                                elseif (is_array($value))
                                {
                                    return array_slice($value,$offset,$limit);
                                }
                            }
                            return $value;
                        }
                    };
                }
            };
        }
        return null;
    }
}