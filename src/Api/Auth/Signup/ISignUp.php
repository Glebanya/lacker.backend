<?php


namespace App\Api\Auth\Signup;

use App\Entity\Client;

interface ISignUp
{
    public function setData(array $params) : self;
    public function getUserData() : Client;
}