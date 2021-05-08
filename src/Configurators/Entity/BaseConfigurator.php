<?php

namespace App\Configurators\Entity;

use App\Api\Collections\MethodBuilderCollectionInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;
use App\Api\ConfiguratorInterface;
use App\Configurators\ClosureMethodCollection;
use App\Configurators\ReflectionReferenceCollection;
use Exception;
use ReflectionException;

abstract class BaseConfigurator implements ConfiguratorInterface
{
	/**
	 * @var PropertyBuilderCollectionInterface $propertyBuilderCollection
	 */
	protected PropertyBuilderCollectionInterface $propertyBuilderCollection;

	/**
	 * @var ReferenceBuilderCollectionInterface $referenceBuilderCollection
	 */
	protected ReferenceBuilderCollectionInterface $referenceBuilderCollection;

	/**
	 * @var MethodBuilderCollectionInterface $methodBuilderCollection
	 */
	protected MethodBuilderCollectionInterface $methodBuilderCollection;

	/**
	 * BaseConfigurator constructor.
	 */
	public function __construct()
	{
		if (!class_exists($class = $this->getEntity()))
		{
			throw new Exception("$class not exists");
		}
	}

	/**
	 * @return string
	 */
	protected abstract function getEntity(): string;

	/**
	 * @return PropertyBuilderCollectionInterface
	 * @throws ReflectionException
	 */
	public function getPropertyBuilderCollection(): PropertyBuilderCollectionInterface
	{
		return $this->propertyBuilderCollection = $this->propertyBuilderCollection
			??
			new ReflectionReferenceCollection($this->getEntity());
	}

	/**
	 * @return ReferenceBuilderCollectionInterface
	 * @throws ReflectionException
	 */
	public function getReferenceBuilderCollection(): ReferenceBuilderCollectionInterface
	{
		return $this->referenceBuilderCollection = $this->referenceBuilderCollection
			??
			new ReflectionReferenceCollection($this->getEntity());

	}

	/**
	 * @return MethodBuilderCollectionInterface
	 */
	public function getMethodBuilderCollection(): MethodBuilderCollectionInterface
	{
		return $this->methodBuilderCollection = $this->methodBuilderCollection
			??
			new ClosureMethodCollection($this->getMethodsList());
	}

	protected function getMethodsList(): array
	{
		return [];
	}
}