<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 * @ORM\Table(name="`lacker_dish`")
 */
class Dish
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
    private ?\DateTimeInterface $created_date;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $update_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $enable;

    /**
     * @ORM\OneToMany(targetEntity=DishPortion::class, mappedBy="dish", orphanRemoval=true)
     */
    private ArrayCollection $dishPortions;

    /**
     * @ORM\ManyToOne(targetEntity=menu::class, inversedBy="dishes")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?menu $menu;

    public function __construct()
    {
        $this->dishPortions = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeInterface $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * @return Collection|DishPortion[]
     */
    public function getDishPortions(): Collection
    {
        return $this->dishPortions;
    }

    public function addDishPortion(DishPortion $dishPortion): self
    {
        if (!$this->dishPortions->contains($dishPortion)) {
            $this->dishPortions[] = $dishPortion;
            $dishPortion->setDish($this);
        }

        return $this;
    }

    public function removeDishPortion(DishPortion $dishPortion): self
    {
        if ($this->dishPortions->removeElement($dishPortion)) {
            // set the owning side to null (unless already changed)
            if ($dishPortion->getDish() === $this) {
                $dishPortion->setDish(null);
            }
        }

        return $this;
    }

    public function getMenu(): ?menu
    {
        return $this->menu;
    }

    public function setMenu(?menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }
}
