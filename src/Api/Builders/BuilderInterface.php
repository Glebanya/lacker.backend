<?php


namespace App\Api\Builders;


interface BuilderInterface
{
    public function build(object $object) : mixed;
}