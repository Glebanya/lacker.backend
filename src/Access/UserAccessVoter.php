<?php

namespace App\Access;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAccessVoter extends Voter
{

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		return
			$subject instanceof User and
			($user = $token->getUser()) and
			$user instanceof User and
			$user->isEqualTo($user)
			;
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof User;
	}
}