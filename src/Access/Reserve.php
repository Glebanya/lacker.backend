<?php

namespace App\Access;

use App\Entity\TableReserve;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class Reserve extends Voter
{

	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
	{
		// TODO: Implement voteOnAttribute() method.
	}

	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof TableReserve;
	}
}