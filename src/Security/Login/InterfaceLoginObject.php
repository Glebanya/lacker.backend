<?php

namespace App\Security\Login;

use App\Entity\Staff;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface InterfaceLoginObject
{
	public function setData(array $params): self;

	public function findUser(): UserInterface|User|Staff|null;

	public function createUser(): UserInterface|User|Staff|null;
}