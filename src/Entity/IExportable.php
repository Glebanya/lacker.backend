<?php


namespace App\Entity;


interface IExportable
{
    public function export(string $locale) : array;
}