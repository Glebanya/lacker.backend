<?php

namespace App\API;

use App\API\Attributes\ApiAttribute;
use App\API\Attributes\Property;
use ReflectionClass;

final class ApiObject
{
    private array $properties = [];

    public function __construct(string|object $class)
    {
        $reflection = new ReflectionClass($class);
        foreach (array_merge_recursive($reflection->getProperties(),$reflection->getMethods()) as $property)
        {
            if (!empty($property->getAttributes(Property::class)))
            {
                $this->addApiProperty(new ApiProperty($property));
            }
        }
    }

    private function getFieldsNames(array $properties): array
    {
        return array_map(
            function (ApiProperty $property) : string{
                return $property->getPropertyName();
            },
            $properties
        );
    }

    private function addApiProperty(ApiProperty $property)
    {
        $this->properties[$property->getPropertyName()] = $property;
    }

    public function getReferenceFieldsNames(): array
    {
        return $this->getFieldsNames($this->getReferenceFields());
    }

    public function getReferenceFields() : array
    {
        return array_filter(
            $this->properties,
            function (ApiProperty $property){
                return $property->isReference();
            }
        );
    }

    public function getScalarFieldsNames(): array
    {
        return $this->getFieldsNames($this->getScalarFields());
    }

    public function getScalarFields() : array
    {
        return array_filter(
            $this->properties,
            function (ApiProperty $property){
                return $property->isScalar();
            }
        );
    }

    public function getDefaultScalarFields() : array
    {
        return array_filter(
            $this->getScalarFields(),
            function (ApiProperty $property){
                return $property->isDefault();
            }
        );
    }

    public function getDefaultScalarFieldsNames() : array
    {
        return $this->getFieldsNames($this->getDefaultScalarFields());
    }

    public function getMethodsNames(): array
    {
        return $this->getFieldsNames($this->getMethods());
    }

    public function getMethods() : array
    {
        return array_filter(
            $this->properties,
            function (ApiProperty $property){
                return $property->isMethod();
            }
        );
    }

    public function getField(string $name) : ApiProperty|null
    {
        if (array_key_exists($name,$this->properties))
        {
            return $this->properties[$name];
        }
        return null;
    }

}