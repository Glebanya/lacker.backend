<?php


namespace App\API\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
interface ApiAttribute
{
    public function getName() : string;
}