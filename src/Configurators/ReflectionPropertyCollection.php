<?php

namespace App\Configurators;

use Exception;
use \ReflectionClass;
use App\Api\Builders\PropertyBuilderInterface;
use App\Api\Collections\PropertyBuilderCollectionInterface;
use App\Api\Properties\PropertyInterface;
use App\Configurators\Attributes\Field;
use ReflectionException;
use ReflectionProperty;

/**
 * Class ReflectionPropertyCollection
 * @package App\Configurators
 */
class ReflectionPropertyCollection implements PropertyBuilderCollectionInterface
{

	/**
	 * @var Field[]
	 */
	protected array $array;

	/**
	 *  constructor.
	 *
	 * @param string|object $entity
	 *
	 * @throws ReflectionException
	 */
	public function __construct(protected string|object $entity)
	{
		$reflection = new ReflectionClass($this->entity);
		$this->array = [] +
			array_reduce(
				$reflection->getProperties(),
				function(array $result, ReflectionProperty $property) : array {
					if (!empty($values = $property->getAttributes(Field::class)))
					{
						foreach ($values as $reflectionAttribute)
						{
							$attribute = $reflectionAttribute->newInstance();
							$result[$attribute->name] = $attribute;
						}
					}

					return $result;
				},
				[]
			) +
			array_reduce(
				$reflection->getAttributes(Field::class),
				function(array $result, \ReflectionAttribute $reflectionAttribute) : array {
					$attribute = $reflectionAttribute->newInstance();
					$result[$attribute->name] = $attribute;

					return $result;

				},
				[]
			);
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
			return new class ($this->array[$property]) implements PropertyBuilderInterface {

				public function __construct(private Field $field)
				{
				}

				public function build(object $object): PropertyInterface
				{
					return new class($object, $this->field) implements PropertyInterface {
						private string $name;
						private \ReflectionMethod $getter;
						private \ReflectionMethod|null $setter;
						private bool $immutable;
						public function __construct(private object $object,Field $field)
						{
							$this->name = $field->name;
							$this->immutable = $field->immutable;
							$reflectionClass = new ReflectionClass($this->object);
							$this->getter = $reflectionClass->getMethod($field->getter);
							if (!$this->immutable && $field->setter)
							{
								$this->setter = $reflectionClass->getMethod($field->setter);
							}
						}

						public function value(): mixed
						{
							return $this->getter->invoke($this->object);
						}

						public function set($parameter)
						{
							if (!$this->immutable && $this->setter)
							{
								return $this->setter->invoke($this->object,$parameter);
							}
							throw new Exception("property $this->name is immutable");
						}
					};
				}
			};
		}

		return null;
	}

	/**
	 * @param string $property
	 *
	 * @return bool
	 */
	public function has(string $property): bool
	{
		return array_key_exists(key: $property, array: $this->array);
	}

	public function getFullNames(): array
	{
		return array_keys($this->array);
	}

	public function getDefaults(): array
	{
		return array_keys(
			array_filter(
				$this->array,
				function(Field $item) {
					return $item->default;
				}
			)
		);
	}
}

