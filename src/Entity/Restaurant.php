<?php

namespace App\Entity;

use App\API\Attributes\Property;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use App\API\Attributes\Field;
use App\API\Attributes\ReferenceField;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
class Restaurant extends BaseObject
{

    /**
     * @ORM\Column(type="json")
     */
    #[Property]
    #[Field(name: 'name')]
    private array $name = [];

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="restaurant", orphanRemoval=true)
     */
    private Collection $orders;

    /**
     * @ORM\OneToMany(targetEntity=Staff::class, mappedBy="restaurant", orphanRemoval=true)
     */
    private Collection $staff;

    /**
     * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="restaurant")
     */
    private Collection $dishes;


    #[Pure] public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->staff = new ArrayCollection();
        $this->dishes = new ArrayCollection();
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
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setRestaurant($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getRestaurant() === $this) {
                $order->setRestaurant(null);
            }
        }

        return $this;
    }

    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(Staff $staff): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff[] = $staff;
            $staff->setRestaurant($this);
        }

        return $this;
    }

    public function removeStaff(Staff $staff): self
    {
        if ($this->staff->removeElement($staff)) {
            // set the owning side to null (unless already changed)
            if ($staff->getRestaurant() === $this) {
                $staff->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    #[Property]
    #[ReferenceField(name: 'dish',referenceClass: Dish::class)]
    public function getAllDishes(): Collection
    {
        return $this->dishes;
    }

//    public function getValidDishes(): Collection
//    {
//        return $this->dishes->filter(function (Dish $dish){
//           return in_array($dish->getId(),$this->stopList);
//        });
//    }

//    public function getNotValidDishes() : Collection
//    {
//        return $this->dishes->filter(function (Dish $dish){
//            return !in_array($dish->getId(),$this->stopList);
//        });
//    }

    public function addDish(Dish $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
            $dish->setRestaurant($this);
        }

        return $this;
    }

    public function removeDish(Dish $dish): self
    {
        if ($this->dishes->removeElement($dish)) {
            // set the owning side to null (unless already changed)
            if ($dish->getRestaurant() === $this) {
                $dish->setRestaurant(null);
            }
        }

        return $this;
    }


    public function setStopList(array $stopList): self
    {
        $this->stopList = $stopList;

        return $this;
    }
}
