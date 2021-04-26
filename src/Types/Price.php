<?php

namespace App\Types;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Price extends \ArrayObject implements \JsonSerializable
{
	public function __construct($array = [])
	{
		parent::__construct($array);
	}

	#[Assert\Callback]
	public function validate(ExecutionContextInterface $context, $payload)
	{
		foreach ($this->getIterator() as $key => $value)
		{
			if (!is_string($key))
			{
				$context->buildViolation("unknown currency $key")->atPath("currency")->addViolation();
			}
			elseif (!in_array($key,['RUB']))
			{
				$context->buildViolation("unsupported currency $key")->atPath("currency")->addViolation();
			}
			if (!is_numeric($value))
			{
				$context->buildViolation("incorrect price value")->atPath("price")->addViolation();
			}
			elseif (!preg_match('/^(?!0+)([0-9]+)(\.[0-9]{1,2}){0,1}$/',(string) $value))
			{
				$context->buildViolation("incorrect price precision")->atPath("price")->addViolation();
			}
		}
	}

	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}
}