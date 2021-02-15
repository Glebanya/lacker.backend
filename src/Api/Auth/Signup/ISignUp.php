<?php


namespace App\Api\Auth\Signup;


interface ISignUp
{
    public function setData(array $params) : self;
    public function getUserData();
}