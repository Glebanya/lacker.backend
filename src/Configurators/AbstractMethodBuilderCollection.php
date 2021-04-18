<?php


namespace App\Configurators;


use App\Api\Builders\MethodBuilderInterface;
use App\Api\Collections\MethodBuilderCollectionInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractMethodBuilderCollection implements MethodBuilderCollectionInterface
{
    public function __construct(protected EntityManagerInterface $manager,)
    {}

    protected abstract function getMethods() : array;

    public function has(string $property): bool
    {
        return in_array($property,$this->getMethods());
    }

    public function get(string $property): MethodBuilderInterface|null
    {
        // TODO: Implement get() method.
    }
}