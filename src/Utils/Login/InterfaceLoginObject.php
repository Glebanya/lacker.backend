<?php


namespace App\Utils\Login;


use App\Entity\User;
use App\Entity\Staff;
use Symfony\Component\Security\Core\User\UserInterface;

interface InterfaceLoginObject
{
    public function setData(array $params) : self;
    public function findUser() : UserInterface|User|Staff|null;
    public function createUser() :  UserInterface|User|Staff|null;
}