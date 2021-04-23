<?php

namespace App\Access;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

interface InterfaceEntityRights
{
	public const VIEW = 'View';
	public const EDIT = 'Edit';
	public const DELETE = 'Delete';
	public const ADD = 'Add';

	public function __construct(string $attribute, $object, TokenInterface $token, AccessVoter $accessVoter);

	public static function getAttributes(): array;

	public function checkAccess(): bool;

}