<?php


namespace App\Api\Properties;


interface PropertyInterface
{
    public function value() : mixed;

    public function set($parameter);
}