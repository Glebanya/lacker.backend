<?php


namespace App\Api\Builders;


use App\Api\Properties\MethodInterface;

interface MethodBuilderInterface extends BuilderInterface
{
    public function build(object $object): MethodInterface;
}