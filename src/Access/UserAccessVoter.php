<?php

namespace App\Access;

use App\Entity\Staff;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAccessVoter extends Voter
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
		return $subject instanceof User and ($user = $token->getUser()) and $user instanceof User and $user->isEqualTo($user);
	}

	protected function canDelete($subject, TokenInterface $token): bool
	{
		return false;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof User;
	}
}