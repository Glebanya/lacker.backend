<?php


namespace App\API\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD)]
final class Field implements IAPIAttribute
{
    public function __construct(
      public string $name,
      public bool $default = false
    )
    {}
}