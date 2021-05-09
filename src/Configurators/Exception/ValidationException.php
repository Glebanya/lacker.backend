<?php

namespace App\Configurators\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
	public function __construct(private ConstraintViolationListInterface $violations)
	{
		parent::__construct((string) $this->violations);
	}

	public function getMessages(): array
	{
		$messages = [];
		foreach ($this->violations as $violationList)
		{
			foreach ($violationList as $violation)
			{
				$messages[] = $violation->getMessage();
			}
		}
		return $messages;
	}

}