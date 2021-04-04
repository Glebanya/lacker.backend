<?php


namespace App\API\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Field implements ApiAttribute
{
    public function __construct(
      public string $name,
      public bool $default = false
    )
    {}

    public function getName(): string
    {
        return $this->name;
    }
}