<?php


namespace App\Api\Builders;


use App\Api\Properties\ReferenceInterface;

interface ReferenceBuilderInterface extends BuilderInterface
{
    public function build(object $object): ReferenceInterface;
}