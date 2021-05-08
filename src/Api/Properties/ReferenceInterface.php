<?php


namespace App\Api\Properties;


interface ReferenceInterface
{
    public function value(array $params) : object|iterable|null;
}