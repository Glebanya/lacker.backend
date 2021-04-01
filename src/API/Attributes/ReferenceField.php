<?php


namespace App\API\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD)]
class ReferenceField implements IAPIAttribute
{
    public function __construct(
        public string $name,
        public string $reference,
        public bool $runtime = false,
        public bool $multi = false
    )
    {}
}