<?php

namespace App\Entity;

use App\API\Attributes\Property;
use App\Repository\PortionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\API\Attributes\Field;
use App\API\Attributes\ReferenceField;
/**
 * @ORM\Entity(repositoryClass=PortionRepository::class)
 */
class Portion extends BaseObject
{

    /**
     * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="portions")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Property]
    #[ReferenceField(name: 'dish',referenceClass: Restaurant::class)]
    private ?Dish $dish;

    /**
     * @ORM\Column(type="json")
     */
    #[Property]
    #[Field(name: 'price')]
    private array $price = [];

    /**
     * @ORM\Column(type="json")
     */
    #[Property]
    #[Field(name: 'size')]
    private array $size = [];

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): self
    {
        $this->dish = $dish;
        return $this;
    }

    public function getPrice(): ?array
    {
        return $this->price;
    }

    public function setPrice(array $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getSize(): ?array
    {
        return $this->size;
    }

    public function setSize(array $size): self
    {
        $this->size = $size;
        return $this;
    }
}
