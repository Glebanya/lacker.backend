<?php

namespace App\Entity;

use App\Types\Lang;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 * @HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.restaurant')]
class Restaurant extends BaseObject
{

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'name')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $name;

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="restaurant")
	 */
	#[Reference('orders')]
	#[CollectionAttribute]
	private Collection $orders;

	/**
	 * @ORM\OneToMany(targetEntity=Staff::class, mappedBy="restaurant",orphanRemoval=true)
	 */
	#[Reference('staff')]
	#[CollectionAttribute]
	private Collection $staff;

	/**
	 * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="restaurant",orphanRemoval=true)
	 */
	#[Reference('dishes')]
	#[CollectionAttribute]
	private Collection $dishes;

	/**
	 * @ORM\OneToMany(targetEntity=Table::class, mappedBy="restaurant", orphanRemoval=true)
	 */
	#[Reference('tables')]
	#[CollectionAttribute]
	private Collection $tables;

	/**
	 * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="restaurant", orphanRemoval=true)
	 */
	#[Reference('menus')]
	#[CollectionAttribute]
	private Collection $menus;

	public function __construct($params = [])
	{
		$this->orders = new ArrayCollection();
		$this->staff = new ArrayCollection();
		$this->tables = new ArrayCollection();
		$this->menus = new ArrayCollection();
	}

	public function getName(): ?Lang
	{
		return $this->name;
	}

	public function setName(array $name): self
	{
		$this->name = new Lang($name);

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
	public function getTables(): Collection
	{
		return $this->tables;
	}

	public function addTable(Table $table): self
	{
		if (!$this->tables->contains($table))
		{
			$this->tables[] = $table;
			$table->setRestaurant($this);
		}

		return $this;
	}

	public function removeTable(Table $table): self
	{
		if ($this->tables->removeElement($table))
		{
			if ($table->getRestaurant() === $this)
			{
				$table->setRestaurant(null);
			}
		}

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
		if (!$this->menus->contains($menu))
		{
			$this->menus[] = $menu;
			$menu->setRestaurant($this);
		}

		return $this;
	}

	public function removeMenu(Menu $menu): self
	{
		if ($this->menus->removeElement($menu))
		{
			if ($menu->getRestaurant() === $this)
			{
				$menu->setRestaurant(null);
			}
		}

		return $this;
	}

	/**
	 * @PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
	}

	/**
	 * @PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
	}
}
