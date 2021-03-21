<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu implements IExportable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="json")
     */
    private array $name = [];

    /**
     * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="menu")
     */
    private Collection $dishes;


    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Restaurant $restaurant;

    /**
     * @ORM\Column(type="json")
     */
    private array $description = [];

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(Dish $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
            $dish->setMenu($this);
        }

        return $this;
    }

    public function removeDish(Dish $dish): self
    {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getMenu() === $this) {
                $dish->setMenu(null);
            }
        }

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

    public function export(string $locale): array
    {
        return [
            'name' => array_key_exists($locale, $this->name)? $this->name[$locale] ?? '' : '',
            'dishes' => $this->getDishes()->map(function ($dish) use ($locale) {
                if ($dish instanceof Dish)
                {
                    return $dish->export($locale);
                }
                return null;
            })->filter(function ($serialized) {
                return isset($serialized) && is_array($serialized);
            })->toArray(),
        ];
    }
}
