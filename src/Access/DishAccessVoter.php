<?php

namespace App\Access;

use App\Entity\Dish;
use App\Entity\Portion;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DishAccessVoter extends Voter
{

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if ($subject instanceof Dish)
		{
			if ($attribute === 'view')
			{
				return true;
			}
			elseif (
				($user = $token->getUser()) and
				$user instanceof Staff and
				$user->getRestaurant()->getId()->compare($subject->getMenu()->getRestaurant()->getId()) === 0
			)
			{
				return in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER]);
			}
		}
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Dish;
	}
}