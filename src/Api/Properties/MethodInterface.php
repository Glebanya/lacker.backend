<?php


namespace App\Api\Properties;


interface MethodInterface
{
    public function execute(array $parameters) : mixed;
}