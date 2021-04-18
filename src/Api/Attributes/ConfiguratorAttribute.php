<?php


namespace App\Api\Attributes;


use App\Api\ConfiguratorInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ConfiguratorAttribute
{
    public function __construct(public string $entity)
    {
        if (!class_exists($this->entity) && $this->entity instanceof ConfiguratorInterface)
        {
            throw new \Exception("class: $this->entity not exists");
        }
    }
}