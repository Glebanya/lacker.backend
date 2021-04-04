<?php


namespace App\API\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ReferenceField implements ApiAttribute
{

    public function __construct(
        public string $name,
        public string $referenceClass
    )
    {
        if (!class_exists($this->referenceClass))
        {
            throw new \Exception("class: $this->referenceClass not exists");
        }
    }

    public function getName(): string
    {
        return $this->name;
    }
}