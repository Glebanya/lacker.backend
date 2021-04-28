<?php

namespace App\Api\Collections;

use App\Api\Builders\ReferenceBuilderInterface;

interface ReferenceBuilderCollectionInterface extends CollectionInterface
{
	public function get(string $property): ReferenceBuilderInterface|null;
}