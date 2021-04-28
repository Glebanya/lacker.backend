<?php

namespace App\Access;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserAccessVoter extends AbstractAccessVoter
{

	protected function getAttributes(): array
	{
		return [];
	}

	protected function getEntity(): string
	{
		return User::class;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if (parent::voteOnAttribute($attribute,$subject,$token) && $subject instanceof User)
		{
			if (
				($user = $token->getUser()) &&
				$user instanceof User &&
				$user->isEqualTo($user)
			)
			{
				return true;
			}

		}
		return false;
	}
}