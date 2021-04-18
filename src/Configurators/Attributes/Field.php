<?php


namespace App\Configurators\Attributes;


use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Field
{
    public function __construct(public string $name)
    {}
}