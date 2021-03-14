<?php


namespace App\Api\Auth\Signup;

use App\Entity\Client;
use App\Entity\Stuff;
use Doctrine\ORM\EntityManagerInterface;

interface ISignUp
{
    public function __construct(EntityManagerInterface $entityManager);
    public function setData(array $params) : self;
    public function findUser() : Client|Stuff|null;
    public function createUser() : Client|Stuff|null;
}