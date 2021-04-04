<?php


namespace App\API;


use Exception;

class ObjectWrapper
{
    private ApiObject $apiObject;

    /**
     * ObjectWrapper constructor.
     * @param object $object
     */
    public function __construct(
        private object $object,
    )
    {
        $this->apiObject = new ApiObject($this->object);
    }

    /**
     * @return ApiObject
     */
    public function getApiObject() : ApiObject
    {
        return $this->apiObject;
    }

    /**
     * @param array $fields
     * @return object|int|array|string|null
     * @throws \ReflectionException
     * @throws Exception
     */
    public function getFields(array|null $fields): object|int|array|string|null
    {
        $fields = $fields ?? $this->apiObject->getDefaultScalarFieldsNames();
        if (empty($unknownFields =  array_diff($fields,$this->apiObject->getScalarFieldsNames())))
        {
            $result = [];
            foreach ($fields as $field)
            {
                $result[$field] = $this->apiObject->getField($field)->invoke($this->object);
            }
            return $result;

        }
        throw new Exception();
    }

    /**
     * @param string $ref
     * @param array|null $fields
     * @param array|null $params
     * @return object|int|array|string|null
     * @throws \ReflectionException
     */
    public function getReference(string $ref, array|null $fields, array|null $params = null): object|int|array|string|null
    {
        if (in_array($ref,$this->apiObject->getReferenceFieldsNames()))
        {
            $objects = $this->apiObject->getField($ref)->invoke($this->object,$params);
            if (is_iterable($objects))
            {
                $result = [];
                foreach ($objects as $object)
                {
                    $result[] = (new static($object))->getFields($fields);
                }
                return $result;
            }
            return (new static($objects))->getFields($fields);
        }
        throw new Exception("unknown ref: $ref");
    }

    /**
     * @param string $methodName
     * @param array|null $params
     * @return object|int|array|string|null
     * @throws Exception
     */
    public function executeMethod(string $methodName,?array $params): object|int|array|string|null
    {
        if (in_array($methodName,$this->apiObject->getReferenceFields()))
        {
            return $this->apiObject->getField($methodName)->invoke($this->object,$params);
        }
        throw new Exception();
    }
}