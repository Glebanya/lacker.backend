<?php


namespace App\Configurators;


use App\Api\Builders\PropertyBuilderInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Properties\PropertyInterface;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use Symfony\Component\Uid\Uuid;

class AnnotationPropertyBuilderCollection implements PropertyBuilderCollectionInterface
{
    protected \ArrayObject $array;

    protected function getProperties() : array
    {
        $result = [];
        foreach ((new \ReflectionClass($this->entity))->getProperties() as $property)
        {
            if (!empty($values = $property->getAttributes(Field::class)))
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

    public function get(string $property): PropertyBuilderInterface|null
    {
        if ($this->has($property))
        {
            return new class ($this->array->offsetGet($property)) implements PropertyBuilderInterface
            {
                public function __construct(private \ReflectionProperty $property)
                {}
                public function build(object $object): PropertyInterface
                {
                    return new class($object,$this->property) implements PropertyInterface {

                        public function __construct(private object $object, private \ReflectionProperty $property)
                        {}
                        public function value(array $params = []): mixed
                        {
                            $this->property->setAccessible(true);
                            $value = $this->property->getValue($this->object);
                            if (!empty($property = $this->property->getAttributes(LangProperty::class)))
                            {
                                $lang = $params['lang'] ?? current($property)->newInstance()->default;
                                if (is_array($value) || $value instanceof \ArrayAccess)
                                {
                                    return $value[$lang];
                                }
                            }
                            elseif ($value instanceof \DateTimeInterface)
                            {
                                $value = $value->getTimestamp();
                            }
                            elseif ($value instanceof Uuid)
                            {
                                $value = base64_encode($value->jsonSerialize());
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