<?php

namespace App\Types;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Lang extends \ArrayObject implements \JsonSerializable
{
	public function __construct(array|\ArrayObject $array = [])
	{
		parent::__construct((array) $array);
	}

	#[Assert\Callback]
	public function validate(ExecutionContextInterface $context, $payload)
	{
		foreach ($this->getIterator() as $key => $value)
		{
			if (!is_string($key))
			{
				$context->buildViolation("unknown lang $key")->atPath("lang")->addViolation();
			}
			elseif (!in_array($key,['ru']))
			{
				$context->buildViolation("unsupported lang $key")->atPath("lang")->addViolation();
			}
			if (!is_string($value))
			{
				$context->buildViolation("incorrect lang phrase")->atPath("phrase")->addViolation();
			}
			elseif ($value <> '')
			{
				$context->buildViolation("lang phrase is empty")->atPath("phrase")->addViolation();
			}
		}
	}

	public function jsonSerialize()
	{
		return $this->getArrayCopy();
	}
}