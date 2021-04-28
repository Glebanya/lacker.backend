<?php

namespace App\Access;

use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class StaffAccessVoter extends AbstractAccessVoter
{
	protected function getAttributes(): array
	{
		return [];
	}

	protected function getEntity(): string
	{
		return Staff::class;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if (parent::voteOnAttribute($attribute,$subject,$token) && $subject instanceof Staff)
		{
			if (
				($user = $token->getUser()) &&
				$user instanceof Staff &&
				$user->getRestaurant()->getId()->compare($subject->getRestaurant()->getId()) === 0
			)
			{
				return true;
			}

		}
		return false;
	}
}