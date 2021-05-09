<?php

namespace App\Configurators\Exception;


class EntityNotFoundException extends \Exception
{
	public function __construct(public string $entityId,)
	{
		parent::__construct("$this->entityId can't find");
	}

	public function getMessages(): array
	{
		return [$this->getMessage()];
	}
}