<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @ORM\Table(name="`lacker_menu`")
 */
class Menu implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $creation_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $update_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $enable;

    /**
     * @ORM\ManyToMany(targetEntity=Restaurant::class, inversedBy="menus")
     */
    private Collection $restaurants;

    /**
     * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="menu", orphanRemoval=true)
     */
    private Collection $dishes;

    public function __construct() {
        $this->restaurants = new ArrayCollection();
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?Uuid {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface {
        return $this->update_date;
    }

    public function setUpdateDate(\DateTimeInterface $update_date): self {
        $this->update_date = $update_date;
        return $this;
    }

    public function getEnable(): ?bool {
        return $this->enable;
    }

    public function setEnable(bool $enable): self {
        $this->enable = $enable;

        return $this;
    }

    /**
     * @return Collection|Restaurant[]
     */
    public function getRestaurants(): Collection {
        return $this->restaurants;
    }

    public function addRestaurant(Restaurant $restaurant): self {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants[] = $restaurant;
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): self {
        $this->restaurants->removeElement($restaurant);

        return $this;
    }

    /**
     * @return Collection|Dish[]
     */
    public function getDishes(): Collection {
        return $this->dishes;
    }

    public function addDish(Dish $dish): self {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
            $dish->setMenu($this);
        }

        return $this;
    }

    public function removeDish(Dish $dish): self {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getMenu() === $this) {
                $dish->setMenu(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId()->jsonSerialize(),
            'timestamp' => $this->getUpdateDate()->getTimestamp(),
            'items' => $this->getDishes()->map(function ($dish) {
                    if($dish instanceof Dish && $dish->getEnable()) {
                        return $dish->jsonSerialize();
                    }
                    return null;
                })->filter(function ($dishSerialized) {
                return isset($dishSerialized) && is_array($dishSerialized);
            })->toArray(),
        ];
    }
}
