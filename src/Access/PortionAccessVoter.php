<?php

namespace App\Access;

use App\Entity\Portion;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PortionAccessVoter extends AbstractAccessVoter
{

	protected function getAttributes(): array
	{
		return [];
	}

	protected function getEntity(): string
	{
		return Portion::class;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if (parent::voteOnAttribute($attribute,$subject,$token) && $subject instanceof Portion)
		{
			if ($attribute === 'view')
			{
				return true;
			}
			elseif (
				($user = $token->getUser()) &&
				$user instanceof Staff &&
				$user->getRestaurant()->getId()->compare($subject->getDish()->getRestaurant()->getId()) === 0
			)
			{
				return true;
			}

		}
		return false;
	}
}