<?php

namespace App\Access\EntitiesRights;

use App\Access\AccessVoter;
use App\Access\InterfaceEntityRights;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class EntityRightsBase implements InterfaceEntityRights
{
	public const ENTITY = '';

	public function __construct(protected string $attribute, protected $object, protected TokenInterface $token,
		protected AccessVoter $accessVoter)
	{
	}

	public static function getAttributes(): array
	{
		return [
			static::DELETE,
			static::VIEW,
			static::EDIT,
			static::ADD
		];
	}
}