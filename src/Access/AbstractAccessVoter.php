<?php

namespace App\Access;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractAccessVoter extends Voter
{

	protected abstract function getAttributes() : array;

	protected abstract function getEntity() : string;

	protected function supports(string $attribute, $subject): bool
	{
		return is_a($subject, $this->getEntity());
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		return in_array($attribute,$this->getAttributes());
	}
}