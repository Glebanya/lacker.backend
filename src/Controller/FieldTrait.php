<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

trait FieldTrait
{
    protected function getSeparator() : string
    {
        return ',';
    }
    protected function getFieldQueryName() : string
    {
        return 'fields';
    }

    protected function getRequestedFields(Request $request, array $expectedFields = [], array $default = []) : array
    {
        if (
            $request->query->has(key:$name = $this->getFieldQueryName())
            && $raw = $request->query->get(key: $this->getFieldQueryName(),default: '')
        )
        {
            $values = explode($this->getSeparator(),$raw);
            if (empty($unknownValues = array_diff($values,$expectedFields)))
            {
                return $values;
            }
            throw new BadRequestException("unknown fields: ",implode(", ",$unknownValues));
        }
        return $default;
    }
}