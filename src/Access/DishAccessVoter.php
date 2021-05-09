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
		return match ($attribute) {
			'view' => $this->canView($subject,$token),
			'update' => $this->canUpdate($subject,$token),
			'delete' => $this->canDelete($subject,$token),
			default => false
		};
	}

	protected function canView($subject, TokenInterface $token): bool
	{
		return true;
	}

	protected function canUpdate($subject, TokenInterface $token): bool
	{
		return
			$subject instanceof Dish and ($user = $token->getUser()) and $user instanceof Staff and
			$user->getRestaurant()->getId()->compare($subject->getMenu()->getRestaurant()->getId()) === 0 and
			in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER])
			;
	}

	protected function canDelete($subject, TokenInterface $token): bool
	{
		return
			$subject instanceof Dish and ($user = $token->getUser()) and $user instanceof Staff and
			$user->getRestaurant()->getId()->compare($subject->getMenu()->getRestaurant()->getId()) === 0 and
			in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER])
			;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Dish;
	}
}