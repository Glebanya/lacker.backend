<?php

namespace App\Access;

use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PortionAccessVoter extends Voter
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
			$subject instanceof Portion and ($user = $token->getUser()) and $user instanceof Staff and
			$user->getRestaurant()->getId()->compare($subject->getDish()->getMenu()->getRestaurant()->getId()) === 0 and
			in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER])
			;
	}

	protected function canDelete($subject, TokenInterface $token): bool
	{
		return
			$subject instanceof Portion and ($user = $token->getUser()) and $user instanceof Staff and
			$user->getRestaurant()->getId()->compare($subject->getDish()->getMenu()->getRestaurant()->getId()) === 0 and
			in_array($user->getRole(),[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_MANAGER])
			;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Portion;
	}
}