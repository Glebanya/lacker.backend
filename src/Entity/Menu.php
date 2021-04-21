<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use App\Configurators\Attributes\Reference;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
#[ConfiguratorAttribute('app.config.menu')]
class Menu extends BaseObject
{
    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'name')]
    #[LangProperty('ru')]
    private array $name = [];


    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Reference(name:'restaurant')]
    private ?Restaurant $restaurant;

    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'description')]
    #[LangProperty('ru')]
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
