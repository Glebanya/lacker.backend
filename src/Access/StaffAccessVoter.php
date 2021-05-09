<?php

namespace App\Access;

use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StaffAccessVoter extends Voter
{
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if (
			$subject instanceof Staff and
			($user = $token->getUser()) and
			$user instanceof Staff and
			$user->getRestaurant()->getId()->compare($subject->getRestaurant()->getId()) === 0
		)
		{
			return in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR]) || $user->isEqualTo($subject);
		}
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Staff;
	}
}