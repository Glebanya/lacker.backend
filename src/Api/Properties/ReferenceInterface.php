<?php


namespace App\Api\Properties;


interface ReferenceInterface
{
    public function value(int $offset, int $limit) : object|array|null;
}