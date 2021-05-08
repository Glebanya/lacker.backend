<?php

namespace App\Configurators;

use \ArrayObject;
use App\Api\Builders\ReferenceBuilderInterface;
use App\Api\Collections\ReferenceBuilderCollectionInterface;
use App\Api\Properties\ReferenceInterface;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use ReflectionClass;
use ReflectionException;


class ReflectionReferenceCollection implements ReferenceBuilderCollectionInterface
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
		foreach ((new ReflectionClass($this->entity))->getMethods() as $property)
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
	 * @return ReferenceBuilderInterface|null
	 */
	public function get(string $property): ReferenceBuilderInterface|null
	{
		if ($this->has($property))
		{
			return new class ($this->array->offsetGet($property)) implements ReferenceBuilderInterface
			{
				public function __construct(private \ReflectionMethod $method)
				{
				}

				public function build(object $object): ReferenceInterface
				{
					return new class($object, $this->method) implements ReferenceInterface
					{

						public function __construct(private object $object, private \ReflectionMethod $method)
						{
						}

						private function getCriteria(array $params) : Criteria
						{
							return Criteria::create()
								->where(
									Criteria::expr()->eq(
										'deleted',
										(bool) array_key_exists('deleted', $params)? $params['deleted'] : false
									)
								)
								->setMaxResults(
									(int) array_key_first($params,'limit')?$params['limit'] : 50
								)
								->setFirstResult(
									(int) array_key_first($params,'offset')? $params['offset'] : 0
								);
						}

						public function value(array $params = []): object|iterable
						{
							$this->method->setAccessible(true);
							if (($value = $this->method->invoke($this->object)) && $value instanceof Selectable)
							{
								return $value->matching($this->getCriteria($params));
							}
							return $value;
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
}