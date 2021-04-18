<?php


namespace App\Api\Collections;


interface CollectionInterface
{
    public function has(string $property) : bool;

    public function get(string $property) : mixed;
}