<?php


namespace App\Configurators\Restaurant;


use App\Api\Collections\MethodBuilderCollectionInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;
use App\Api\ConfiguratorInterface;
use App\Configurators\AbstractPropertyBuilderCollection;
use App\Configurators\AbstractReferenceBuilderCollection;
use App\Entity\Restaurant;

final class RestaurantConfigurator implements ConfiguratorInterface
{
    private AbstractPropertyBuilderCollection $propertyBuilderCollection;

    private AbstractReferenceBuilderCollection $referenceBuilderCollection;

    public function __construct()
    {}

    public function getPropertyBuilderCollection(): PropertyBuilderCollectionInterface
    {
        return $this->propertyBuilderCollection =
            $this->propertyBuilderCollection ?? new class extends AbstractPropertyBuilderCollection {
                protected const ENTITY_CLASS = Restaurant::class;
            };
    }

    public function getReferenceBuilderCollection(): ReferenceBuilderCollectionInterface
    {
        return $this->referenceBuilderCollection =
            $this->referenceBuilderCollection ?? new class extends AbstractReferenceBuilderCollection {
                protected const ENTITY_CLASS = Restaurant::class;
            };
    }

    public function getMethodBuilderCollection(): MethodBuilderCollectionInterface
    {

    }
}