<?php

namespace App\Api\Collections;

use App\Api\Builders\PropertyBuilderInterface;

interface PropertyBuilderCollectionInterface extends CollectionInterface
{
	/**
	 * @param string $property
	 *
	 * @return PropertyBuilderInterface|null
	 */
	public function get(string $property): PropertyBuilderInterface|null;

	/**
	 * @return string[]
	 */
	public function getNames() : array;
}