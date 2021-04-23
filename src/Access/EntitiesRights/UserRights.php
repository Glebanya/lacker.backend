<?php

namespace App\Access\EntitiesRights;

use App\Access\InterfaceEntityRights;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRights extends EntityRightsBase implements InterfaceEntityRights
{
	public static function getAttributes(): array
	{
		return [
			static::DELETE,
			static::VIEW,
			static::EDIT
		];
	}

	public function checkAccess(): bool
	{
		if (($user = $this->token->getUser()) && $user instanceof UserInterface)
		{
			if ($this->object instanceof User)
			{
				return match ($this->attribute)
				{
					static::EDIT, static::DELETE => $this->object->isEqualTo($user),
					static::VIEW => $this->object->isEqualTo($user) ||
						$this->accessVoter->security->isGranted('ROLE_STUFF'),
					default => false
				};

			}
		}

		return false;
	}
}