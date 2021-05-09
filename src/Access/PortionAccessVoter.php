<?php

namespace App\Access;

use App\Entity\Portion;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PortionAccessVoter extends Voter
{
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($subject instanceof Portion)
		{
			if ($attribute === 'view')
			{
				return true;
			}
			elseif (($user = $token->getUser()) && $user instanceof Staff && $user->getRestaurant()->getId()->compare($subject->getDish()->getMenu()->getRestaurant()->getId()) === 0)
			{
				return in_array($user->getRole(), [Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER]);
			}
		}
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Portion;
	}
}