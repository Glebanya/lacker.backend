<?php


namespace App\Api\Collections;


use App\Api\Builders\MethodBuilderInterface;

interface MethodBuilderCollectionInterface extends CollectionInterface
{
    public function get(string $property): MethodBuilderInterface|null;
}