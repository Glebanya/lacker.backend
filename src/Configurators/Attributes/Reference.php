<?php


namespace App\Configurators\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Reference
{
    public function __construct(public string $name)
    {}
}