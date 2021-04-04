<?php


namespace App\API\Attributes;


#[\Attribute(\Attribute::TARGET_METHOD)]
final class Method implements ApiAttribute
{
    public function __construct(
        public string $name
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }
}