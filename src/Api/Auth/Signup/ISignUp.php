<?php


namespace App\Api\Auth\Signup;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

interface ISignUp
{
    public function __construct(EntityManagerInterface $entityManager);
    public function setData(array $params) : self;
    public function findUser() : ?Client;
    public function createUser() : Client;
}