<?php


namespace App\Api\Builders;


use App\Api\Properties\PropertyInterface;

interface PropertyBuilderInterface extends BuilderInterface
{
    public function build(object $object): PropertyInterface;
}