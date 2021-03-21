<?php


namespace App\Entity;


interface IEquatable
{
    public function equalTo(IEquatable $object)  : bool;
}