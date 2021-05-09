<?php

namespace App\Access;

use App\Entity\Restaurant;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RestaurantAccessVoter extends Voter
{
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($subject instanceof Restaurant)
		{
			if ($attribute === 'view')
			{
				return true;
			}
			elseif (
				($user = $token->getUser()) and $user instanceof Staff and
				$user->getRestaurant()->getId()->compare($subject->getId()) === 0
			)
			{
				return in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR]);
			}

		}
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Restaurant;
	}
}