<?php


namespace App\API;


use Exception;
use ReflectionException;
use App\API\Attributes\Field;
use App\API\Attributes\Method;
use App\API\Attributes\ApiAttribute;
use App\API\Attributes\ReferenceField;


class ApiProperty
{
    private ApiAttribute $attribute;

    /**
     * ApiProperty constructor.
     * @param \ReflectionProperty|\ReflectionMethod $reflection
     */
    public function __construct(
        private \ReflectionProperty|\ReflectionMethod $reflection
    )
    {
        $reflectionAttribute = null;
        if (!empty($list = $this->reflection->getAttributes(Field::class)))
        {
            $reflectionAttribute = current($list);
        }
        elseif (!empty($list = $this->reflection->getAttributes(Method::class)))
        {
            $reflectionAttribute = current($list);
        }
        elseif (!empty($list = $this->reflection->getAttributes(ReferenceField::class)))
        {
            $reflectionAttribute = current($list);
        }

        $this->attribute = $reflectionAttribute->newInstance();
    }

    /**
     * @return string
     */
    public function getPropertyName() : string
    {
        return $this->attribute->getName();
    }

    /**
     * @return bool
     */
    public function isScalar() : bool
    {
        return $this->attribute instanceof Field;
    }

    /**
     * @return bool
     */
    public function isMethod() : bool
    {
        return $this->attribute instanceof Method;
    }

    /**
     * @return bool
     */
    public function isReference() : bool
    {
        return $this->attribute instanceof ReferenceField;
    }

    public function isDefault() : bool
    {
        return $this->isScalar() && $this->attribute->default;
    }

    /**
     * @param array|null $params
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    private function args(?array $params) : array
    {
        $results = [];
        $params = $params ?? [];
        foreach ($this->reflection->getParameters() as $parameter)
        {
            if (array_key_exists($parameter->getName(),$params))
            {
                $results[$parameter->getName()] = $params[$parameter->getName()];
            }
            elseif ($parameter->isDefaultValueAvailable())
            {
                $results[$parameter->getName()] = $parameter->getDefaultValue();
            }
            elseif ($parameter->allowsNull())
            {
                $results[$parameter->getName()] = null;
            }
            else
            {
                throw new Exception("No required parameter ".$parameter->getName());
            }
        }
        return $results;
    }

    /**
     * @param object $object
     * @param array|null $parameters
     * @return array|int|string|object|null
     * @throws ReflectionException
     */
    public function invoke(object $object, ?array $parameters = null) : array|int|string|object|null
    {
        $this->reflection->setAccessible(true);

        if ($this->reflection instanceof \ReflectionProperty)
        {
            return $this->reflection->getValue($object);
        }
        elseif ($this->reflection instanceof \ReflectionMethod)
        {
            return $this->reflection->invokeArgs($object,$this->args($parameters));
        }
        return null;
    }
}