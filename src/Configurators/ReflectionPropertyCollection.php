<?php

namespace App\Configurators;

use App\Configurators\Attributes\Immutable;
use App\Entity\Currency;
use App\Types\Image;
use App\Types\Lang;
use DateTimeInterface;
use Exception;
use \ReflectionClass;
use \ArrayObject;
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
	 * @var ArrayObject
	 */
	protected ArrayObject $array;

	/**
	 *  constructor.
	 *
	 * @param string|object $entity
	 *
	 * @throws ReflectionException
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
	 * @return PropertyBuilderInterface|null
	 */
	public function get(string $property): PropertyBuilderInterface|null
	{
		if ($this->has($property))
		{
			return new class ($property,$this->array->offsetGet($property)) implements PropertyBuilderInterface {
				public function __construct(private string $name,private ReflectionProperty $property)
				{
				}

				public function build(object $object): PropertyInterface
				{
					return new class($this->name, $object, $this->property) implements PropertyInterface {

						public function __construct(
							private string $name,
							private object $object,
							private ReflectionProperty $property
						)
						{
						}

						public function value(array $params = []): mixed
						{
							$field = current($this->property->getAttributes(Field::class))->newInstance();
							$reflectionMethod = (new ReflectionClass($this->object))->getMethod($field->getter);
							$value = $reflectionMethod->invoke($this->object);
							if ($value instanceof DateTimeInterface)
							{
								$value = $value->getTimestamp();
							}

							return $value;
						}

						public function set($parameter)
						{
							if (empty($this->property->getAttributes(Immutable::class)))
							{
								if ($this->property->class === Lang::class)
								{
									if (is_array($parameter))
									{
										$parameter = new Lang($parameter);
									}
								}
								elseif ($this->property->class === Currency::class)
								{
									if (is_array($parameter))
									{
										$parameter = new Currency($parameter);
									}
								}
								elseif ($this->property->class === Image::class)
								{
									$parameter = new Image($parameter);
								}

								$field = current($this->property->getAttributes(Field::class))->newInstance();
								(new ReflectionClass($this->object))
									->getMethod($field->setter)
									->invoke($this->object,$parameter);
								return;
							}
							throw new Exception("property $this->name immutable");
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
		return $this->array->offsetExists($property);
	}

	/**
	 * @return string[]
	 */
	public function getNames(): array
	{
		return array_keys((array)$this->array);
	}
}

