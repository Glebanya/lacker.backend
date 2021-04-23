<?php

namespace App\Configurators\Entity;

use ArrayAccess;
use ArrayObject;
use DateTimeInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Uid\Uuid;
use App\Api\Builders\MethodBuilderInterface;
use App\Api\Builders\PropertyBuilderInterface;
use App\Api\Builders\ReferenceBuilderInterface;
use App\Api\Collections\MethodBuilderCollectionInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;
use App\Api\ConfiguratorInterface;
use App\Api\Properties\MethodInterface;
use App\Api\Properties\PropertyInterface;
use App\Api\Properties\ReferenceInterface;
use App\Configurators\Attributes\Collection;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use App\Configurators\Attributes\Reference;
use App\Configurators\ClosureMethodBuilderCollection;

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
	 * @var ClosureMethodBuilderCollection $methodBuilderCollection
	 */
	protected ClosureMethodBuilderCollection $methodBuilderCollection;

	/**
	 * BaseConfigurator constructor.
	 */
	public function __construct()
	{
		if (!class_exists($class = $this->getEntity()))
		{
			throw new \Exception("$class not exists");
		}
	}

	/**
	 * @return string
	 */
	protected abstract function getEntity(): string;

	/**
	 * @return PropertyBuilderCollectionInterface
	 */
	public function getPropertyBuilderCollection(): PropertyBuilderCollectionInterface
	{
		return $this->propertyBuilderCollection = $this->propertyBuilderCollection
			??
			new class ($this->getEntity()) implements PropertyBuilderCollectionInterface {

				/**
				 * @var ArrayObject
				 */
				protected ArrayObject $array;

				/**
				 *  constructor.
				 *
				 * @param string $entity
				 */
				public function __construct(protected string $entity)
				{
					$result = [];
					foreach ((new ReflectionClass($this->entity))->getProperties() as $property)
					{
						if (!empty($values = $property->getAttributes(Field::class)))
						{
							$field = current($values)->newInstance()->name;
							$result[$field] = $property;
						}
					}
					$this->array = new ArrayObject($result);
				}

				/**
				 * @param string $property
				 *
				 * @return bool
				 */
				public function has(string $property): bool
				{
					return $this->array->offsetExists($property);
				}

				/**
				 * @param string $property
				 *
				 * @return PropertyBuilderInterface|null
				 */
				public function get(string $property): PropertyBuilderInterface|null
				{
					if ($this->has($property))
					{
						return new class ($this->array->offsetGet($property)) implements PropertyBuilderInterface {
							public function __construct(private ReflectionProperty $property)
							{
							}

							public function build(object $object): PropertyInterface
							{
								return new class($object, $this->property) implements PropertyInterface {

									public function __construct(private object $object,
										private ReflectionProperty $property)
									{
									}

									public function value(array $params = []): mixed
									{
										$this->property->setAccessible(true);
										$value = $this->property->getValue($this->object);
										if (!empty($property = $this->property->getAttributes(LangProperty::class)))
										{
											$lang = $params['lang'] ?? current($property)->newInstance()->default;
											if (is_array($value) || $value instanceof ArrayAccess)
											{
												return $value[$lang];
											}
										}
										elseif ($value instanceof DateTimeInterface)
										{
											$value = $value->getTimestamp();
										}
										elseif ($value instanceof Uuid)
										{
											$value = base64_encode($value->jsonSerialize());
										}

										return $value;
									}

									public function set($parameter)
									{
										$this->property->setAccessible(true);
										$this->property->setValue($this->object, $parameter);
									}
								};
							}
						};
					}

					return null;
				}
			};
	}

	/**
	 * @return ReferenceBuilderCollectionInterface
	 */
	public function getReferenceBuilderCollection(): ReferenceBuilderCollectionInterface
	{
		return $this->referenceBuilderCollection = $this->referenceBuilderCollection
			??
			new class ($this->getEntity()) implements ReferenceBuilderCollectionInterface {

				/**
				 * @var ArrayObject
				 */
				protected ArrayObject $array;

				/**
				 *  constructor.
				 *
				 * @param string $entity
				 */
				public function __construct(protected string $entity)
				{
					$result = [];
					foreach ((new ReflectionClass($this->entity))->getProperties() as $property)
					{
						if (!empty($values = $property->getAttributes(Reference::class)))
						{
							$field = current($values)->newInstance()->name;
							$result[$field] = $property;
						}
					}
					$this->array = new ArrayObject($result);
				}

				/**
				 * @param string $property
				 *
				 * @return bool
				 */
				public function has(string $property): bool
				{
					return $this->array->offsetExists($property);
				}

				/**
				 * @param string $property
				 *
				 * @return ReferenceBuilderInterface|null
				 */
				public function get(string $property): ReferenceBuilderInterface|null
				{
					if ($this->has($property))
					{
						return new class ($this->array->offsetGet($property)) implements ReferenceBuilderInterface {
							public function __construct(private ReflectionProperty $property)
							{
							}

							public function build(object $object): ReferenceInterface
							{
								return new class($object, $this->property) implements ReferenceInterface {

									public function __construct(private object $object,
										private ReflectionProperty $property)
									{
									}

									public function value(int $offset = 0, int $limit = 1000): object|array
									{
										$this->property->setAccessible(true);
										$value = $this->property->getValue($this->object);
										if (!empty($property = $this->property->getAttributes(Collection::class)))
										{
											if ($value instanceof \Doctrine\Common\Collections\Collection)
											{
												return $value->slice($offset, $limit);
											}
											elseif (is_array($value))
											{
												return array_slice($value, $offset, $limit);
											}
										}

										return $value;
									}
								};
							}
						};
					}

					return null;
				}
			};
	}

	/**
	 * @return MethodBuilderCollectionInterface
	 */
	public function getMethodBuilderCollection(): MethodBuilderCollectionInterface
	{
		return $this->methodBuilderCollection = $this->methodBuilderCollection
			??
			new class ($this->getEntity()) implements MethodBuilderCollectionInterface {

				/**
				 * @var ArrayObject
				 */
				protected ArrayObject $array;

				/**
				 *  constructor.
				 *
				 * @param string $entity
				 */
				public function __construct(protected string $entity)
				{
					$result = [];
					foreach ((new ReflectionClass($this->entity))->getMethods() as $method)
					{
						if (!empty($values = $method->getAttributes(Field::class)))
						{
							$field = current($values)->newInstance()->name;
							$result[$field] = $method;
						}
					}
					$this->array = new ArrayObject($result);
				}

				/**
				 * @param string $property
				 *
				 * @return bool
				 */
				public function has(string $property): bool
				{
					return $this->array->offsetExists($property);
				}

				/**
				 * @param string $property
				 *
				 * @return MethodBuilderInterface|null
				 */
				public function get(string $property): MethodBuilderInterface|null
				{
					if ($this->has($property))
					{
						return new class ($this->array->offsetGet($property)) implements MethodBuilderInterface {

							public function __construct(private ReflectionMethod $method)
							{
							}

							public function build(object $object): MethodInterface
							{
								return new class($object, $this->method) implements MethodInterface {

									public function __construct(private object $object,
										private ReflectionMethod $method)
									{
									}

									public function execute(array $parameters = []): mixed
									{
										$this->method->setAccessible(true);

									}
								};
							}
						};
					}

					return null;
				}
			};
	}
}