<?php

namespace App\Api;

use Traversable;

/**
 * Class ApiEntityCollection
 * @package App\Api
 */
final class ApiEntityCollection extends \ArrayObject implements \IteratorAggregate
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param ApiEntity $entity
	 *
	 * @return $this
	 */
	public function addEntity(ApiEntity $entity): ApiEntityCollection
	{
		$this[] = $entity;
		return $this;
	}
}