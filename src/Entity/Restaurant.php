<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
class Restaurant implements IExportable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;


    /**
     * @ORM\ManyToOne(targetEntity=Business::class, inversedBy="restaurants")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Business $business;

    /**
     * @ORM\ManyToMany(targetEntity=Staff::class, inversedBy="restaurants")
     */
    private Collection $pinnedStaff;

    /**
     * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="restaurant", orphanRemoval=true)
     */
    private Collection $menus;

    /**
     * @ORM\Column(type="json")
     */
    private array $name = [];

    #[Pure] public function __construct()
    {
        $this->pinnedStaff = new ArrayCollection();
        $this->menus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): self
    {
        $this->business = $business;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPinnedStaff(): Collection
    {
        return $this->pinnedStaff;
    }

    public function addPinnedStaff(Staff $pinnedStaff): self
    {
        if (!$this->pinnedStaff->contains($pinnedStaff)) {
            $this->pinnedStaff[] = $pinnedStaff;
        }

        return $this;
    }

    public function removePinnedStaff(Staff $pinnedStaff): self
    {
        $this->pinnedStaff->removeElement($pinnedStaff);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->setRestaurant($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            // set the owning side to null (unless already changed)
            if ($menu->getRestaurant() === $this) {
                $menu->setRestaurant(null);
            }
        }

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

    public function export(string $locale): array
    {
        return [
            'name' => array_key_exists($locale, $this->name)? $this->name[$locale] ?? '' : '',
            'menus' => $this->getMenus()->map(function ($menu) use ($locale) {
                if ($menu instanceof Menu)
                {
                    return $menu->export($locale);
                }
                return null;
            })->filter(function ($serialized) {
                return isset($serialized) && is_array($serialized);
            })->toArray(),
        ];
    }
}
