<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;
use App\API\Attributes\Field;
use App\API\Attributes\ReferenceField;


/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu extends BaseObject
{
    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'name')]
    private array $name = [];


    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
     * @ORM\JoinColumn(nullable=false)
     */
    #[ReferenceField(name:'restaurant',reference: Restaurant::class)]
    private ?Restaurant $restaurant;

    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'description')]
    private array $description = [];

    public function getName(): ?array
    {
        return $this->name;
    }

    public function setName(array $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;
        return $this;
    }
}
