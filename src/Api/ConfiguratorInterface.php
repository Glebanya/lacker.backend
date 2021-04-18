<?php


namespace App\Api;


use App\Api\Collections\MethodBuilderCollectionInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;

interface ConfiguratorInterface
{
    public function getPropertyBuilderCollection() : PropertyBuilderCollectionInterface;
    public function getReferenceBuilderCollection() : ReferenceBuilderCollectionInterface;
    public function getMethodBuilderCollection() : MethodBuilderCollectionInterface;
}