<?php


namespace App\Api\Collections;


use App\Api\Builders\PropertyBuilderInterface;

interface PropertyBuilderCollectionInterface extends CollectionInterface
{
    public function get(string $property): PropertyBuilderInterface|null;
}