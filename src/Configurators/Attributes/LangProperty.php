<?php


namespace App\Configurators\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LangProperty
{
    public function __construct(public string $default)
    {}
}