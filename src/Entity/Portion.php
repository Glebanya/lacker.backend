<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection;
use App\Configurators\Entity\Dish;
use App\Repository\PortionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;

/**
 * @ORM\Entity(repositoryClass=PortionRepository::class)
 */
#[ConfiguratorAttribute('app.config.portion')]
class Portion extends BaseObject
{

    /**
     * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="portions")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Reference(name: 'dish')]
    #[Collection]
    private ?Dish $dish;

    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'price')]
    private array $price = [];

    /**
     * @ORM\Column(type="json")
     */
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
