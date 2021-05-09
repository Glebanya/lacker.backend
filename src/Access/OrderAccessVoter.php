<?php

namespace App\Access;

use App\Entity\Menu;
use App\Entity\Order;
use App\Entity\Staff;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderAccessVoter extends Voter
{
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		return match ($attribute) {
			'view' => $this->canView($subject,$token),
			'update','add_portion','remove_portion' => $this->canUpdate($subject,$token),
			'delete' => $this->canDelete($subject,$token),
			default => false
		};
	}

	protected function canView($subject, TokenInterface $token): bool
	{
		return ($user = $token->getUser()) and $subject instanceof Order and (
			($user instanceof User and $subject->getUser()->isEqualTo($user)) or
			($user instanceof Staff and $subject->getRestaurant()->getId()->equals($user->getRestaurant()->getId()))
		);
	}

	protected function canUpdate($subject, TokenInterface $token): bool
	{
		return ($user = $token->getUser()) and
			$subject instanceof Order and
			$subject->getStatus() === Order::STATUS_NEW and (
				($user instanceof User and $subject->getUser()->isEqualTo($user)) or
				($user instanceof Staff and $subject->getRestaurant()->getId()->equals($user->getRestaurant()->getId()))
			);
	}

	protected function canDelete($subject, TokenInterface $token): bool
	{
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Order;
	}
}