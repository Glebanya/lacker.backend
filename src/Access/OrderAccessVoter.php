<?php

namespace App\Access;

use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderAccessVoter extends Voter
{

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
	{

	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Order;
	}
}