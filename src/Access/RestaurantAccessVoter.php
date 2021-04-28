<?php

namespace App\Access;

use App\Entity\Restaurant;
use App\Entity\Staff;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class RestaurantAccessVoter extends AbstractAccessVoter
{

	protected function getAttributes(): array
	{
		return [];
	}

	protected function getEntity(): string
	{
		return Restaurant::class;
	}

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		if (parent::voteOnAttribute($attribute,$subject,$token) && $subject instanceof Restaurant)
		{
			$user = $token->getUser();
			if ($attribute === 'view')
			{
				return true;
			}
			elseif ($user instanceof Staff && $user->getRestaurant()->getId()->compare($subject->getId()) === 0)
			{
				return true;
			}

		}
		return false;
	}
}