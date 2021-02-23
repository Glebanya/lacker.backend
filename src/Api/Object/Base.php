<?php


namespace App\Api\Object;


interface Base extends \ArrayAccess
{
    public function getResource();
}