<?php


namespace App\Configurators\Entity;


use App\Api\ConfiguratorInterface,
    App\Api\Collections\MethodBuilderCollectionInterface,
    App\Api\Collections\PropertyBuilderCollectionInterface,
    App\Api\Collections\ReferenceBuilderCollectionInterface,
    App\Configurators\AnnotationPropertyBuilderCollection,
    App\Configurators\AnnotationReferenceBuilderCollection;
use App\Configurators\ClosureMethodBuilderCollection;

abstract class BaseConfigurator implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = '';

    protected AnnotationPropertyBuilderCollection $propertyBuilderCollection;

    protected AnnotationReferenceBuilderCollection $referenceBuilderCollection;

    protected ClosureMethodBuilderCollection $methodBuilderCollection;

    protected abstract function getMethods() : array;

    public function getPropertyBuilderCollection(): PropertyBuilderCollectionInterface
    {
        return $this->propertyBuilderCollection =
            $this->propertyBuilderCollection ?? new AnnotationPropertyBuilderCollection(static::ENTITY_CLASS);
    }

    public function getReferenceBuilderCollection(): ReferenceBuilderCollectionInterface
    {
        return $this->referenceBuilderCollection =
            $this->referenceBuilderCollection ?? new AnnotationReferenceBuilderCollection(static::ENTITY_CLASS);
    }

    public function getMethodBuilderCollection(): MethodBuilderCollectionInterface
    {
        return $this->methodBuilderCollection =
            $this->methodBuilderCollection ?? new ClosureMethodBuilderCollection($this->getMethods());
    }
}