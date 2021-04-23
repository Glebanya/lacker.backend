<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 */
#[ConfiguratorAttribute('app.config.restaurant')]
class Restaurant extends BaseObject
{

	/**
	 * @ORM\Column(type="json")
	 */
	#[Field(name: 'name')]
	#[LangProperty('ru')]
	private array $name = [];

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="restaurant",orphanRemoval=true)
	 */
	#[Reference('orders')]
	#[CollectionAttribute]
	private Collection $orders;

	/**
	 * @ORM\OneToMany(targetEntity=Staff::class, mappedBy="restaurant",orphanRemoval=true)
	 */
	private Collection $staff;

	/**
	 * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="restaurant",orphanRemoval=true)
	 */
	#[Reference('dishes')]
	#[CollectionAttribute]
	private Collection $dishes;

	public function __construct($params = [])
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
		if (!$this->orders->contains($order))
		{
			$this->orders[] = $order;
			$order->setRestaurant($this);
		}

		return $this;
	}

	public function removeOrder(Order $order): self
	{
		if ($this->orders->removeElement($order))
		{
			// set the owning side to null (unless already changed)
			if ($order->getRestaurant() === $this)
			{
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
		if (!$this->staff->contains($staff))
		{
			$this->staff[] = $staff;
			$staff->setRestaurant($this);
		}

		return $this;
	}

	public function removeStaff(Staff $staff): bool
	{
		if ($this->staff->removeElement($staff))
		{
			// set the owning side to null (unless already changed)
			if ($staff->getRestaurant() === $this)
			{
				$staff->setRestaurant(null);
			}
		}

		return true;
	}

	/**
	 * @return Collection
	 */
	public function getAllDishes(): Collection
	{
		return $this->dishes;
	}

	public function addDish(Dish $dish): self
	{
		if (!$this->dishes->contains($dish))
		{
			$this->dishes[] = $dish;
			$dish->setRestaurant($this);
		}

		return $this;
	}

	public function removeDish(Dish $dish): bool
	{
		if ($dish->getRestaurant() === $this)
		{
			if ($this->dishes->removeElement($dish))
			{
				$dish->setRestaurant(null);

				return true;
			}
		}

		return false;
	}

	public function setStopList(array $stopList): self
	{
		$this->stopList = $stopList;

		return $this;
	}
}
