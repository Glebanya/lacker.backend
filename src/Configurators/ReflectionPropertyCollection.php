<?php

namespace App\Configurators;

use App\Entity\Currency;
use App\Types\Lang;
use DateTimeInterface;
use \ReflectionClass;
use \ArrayObject;
use App\Api\Builders\PropertyBuilderInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Properties\PropertyInterface;
use App\Configurators\Attributes\Field;
use ReflectionProperty;

/**
 * Class ReflectionPropertyCollection
 * @package App\Configurators
 */
class ReflectionPropertyCollection implements PropertyBuilderCollectionInterface
{

	/**
	 * @var ArrayObject
	 */
	protected ArrayObject $array;

	/**
	 *  constructor.
	 *
	 * @param string|object $entity
	 *
	 * @throws \ReflectionException
	 */
	public function __construct(protected string|object $entity)
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
							if ($value instanceof DateTimeInterface)
							{
								$value = $value->getTimestamp();
							}

							return $value;
						}

						public function set($parameter)
						{
							$this->property->setAccessible(true);
							$parameter = match ($this->property->class)
							{
								Lang::class => new Lang($parameter),
								Currency::class => new Currency($parameter),
								default => $parameter
							};
							$this->property->setValue($this->object, $parameter);
						}
					};
				}
			};
		}

		return null;
	}
};