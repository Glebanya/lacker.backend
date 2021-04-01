<?php

namespace App\API;

use App\API\Attributes\Field;
use App\API\Attributes\ReferenceField;
use ReflectionClass;

final class EntityMeta
{
    private ReflectionClass $reflection;

    private array $properties;

    public function __construct(string $class)
    {
        $this->reflection = new ReflectionClass($class);
        $this->properties = $this->reflection->getProperties();
    }

    public function getReferenceFields() : array
    {
        $result = [];
        foreach ($this->properties as $property)
        {
            if($attribute = current($property->getAttributes(ReferenceField::class)))
            {
                $result[] = $attribute->newInstance();
            }
        }
        return $result;
    }

    public function getScalarFields() : array
    {
        $result = [];
        foreach ($this->properties as $property)
        {
            if($attribute = current($property->getAttributes(Field::class)))
            {
                $result[] = $attribute->newInstance();
            }
        }
        return $result;
    }

    public function getScalarDefaultFields() : array
    {
        $result = [];
        foreach ($this->getScalarFields() as $field)
        {
            if ($field->default)
            {
                $result[] = $field;
            }
        }
        return $result;
    }

    public function getScalarFieldsNames(): array
    {
        $result = [];
        foreach ($this->getScalarFields() as $field)
        {
            $result[] = $field->name;
        }
        return $result;
    }

    public function getDefaultScalarFieldsNames(): array
    {
        $result = [];
        foreach ($this->getScalarDefaultFields() as $field)
        {
            $result[] = $field->name;
        }
        return $result;
    }

    public function getScalarValuesByField(object $object, array $fields) : array
    {
        $result = [];
        if ($object instanceof $this->reflection->name)
        {
            foreach ($this->properties as $property)
            {
                if($attribute = current($property->getAttributes(Field::class)))
                {
                    if(in_array($name = $attribute->newInstance()->name,$fields))
                    {
                        $result[$name] = $property->getValue($object);
                    }
                }
            }
        }
        return $result;
    }

}