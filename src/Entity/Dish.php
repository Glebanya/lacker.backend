<?php

namespace App\Entity;

use App\API\Attributes\Property;
use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\API\Attributes\Field;
use App\API\Attributes\ReferenceField;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 */
class Dish extends BaseObject
{
    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'description')]
    private array $description = [];

    /**
     * @ORM\OneToMany(targetEntity=Portion::class, mappedBy="dish", orphanRemoval=true)
     */
    #[ReferenceField(name: 'portion', referenceClass: Portion::class)]
    private Collection $portions;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="dishes")
     */
    #[ReferenceField(name: 'menu',referenceClass: Menu::class)]
    private ?Menu $menu;

    /**
     * @ORM\Column(type="json")
     */
    #[Field(name: 'name')]
    private array $name = [];

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="dishes")
     * @ORM\JoinColumn(nullable=true)
     */
    #[ReferenceField(name: 'restaurant',referenceClass: Restaurant::class)]
    private ?Restaurant $restaurant;

    public function __construct()
    {
        $this->portions = new ArrayCollection();
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

    /**
     * @return Collection
     */
    public function getPortions(): Collection
    {
        return $this->portions;
    }

    public function addPortion(Portion $portion): self
    {
        if (!$this->portions->contains($portion)) {
            $this->portions[] = $portion;
            $portion->setDish($this);
        }

        return $this;
    }

    public function removePortion(Portion $portion): self
    {
        if ($this->portions->removeElement($portion)) {
            if ($portion->getDish() === $this) {
                $portion->setDish(null);
            }
        }

        return $this;
    }


    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }


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

}
