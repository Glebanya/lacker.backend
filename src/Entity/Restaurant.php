<?php

namespace App\Entity;

use App\Types\Image;
use App\Types\Lang;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RestaurantRepository;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Configurators\Attributes\Collection as CollectionAttribute;

/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.restaurant')]
class Restaurant extends BaseObject
{

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'name')]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $name;

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="restaurant", fetch="EXTRA_LAZY")
	 */
	protected Collection|Selectable $orders;

	/**
	 * @ORM\OneToMany(targetEntity=Staff::class, mappedBy="restaurant",orphanRemoval=true, fetch="EXTRA_LAZY",cascade = {"persist"})
	 */
	protected Collection|Selectable $staff;

	/**
	 * @ORM\OneToMany(targetEntity=Table::class, mappedBy="restaurant", orphanRemoval=true, fetch="EXTRA_LAZY",cascade = {"persist"})
	 */
	protected Collection|Selectable $tables;

	/**
	 * @ORM\OneToMany(targetEntity=Menu::class, mappedBy="restaurant", orphanRemoval=true, fetch="EXTRA_LAZY", cascade = {"persist"})
	 */
	protected Collection|Selectable $menus;

	/**
	 * @ORM\Column(type="image", nullable=true)
	 */
	protected Image $logo;

	/**
	 * Restaurant constructor.
	 */
	public function __construct()
	{
		$this->orders = new ArrayCollection();
		$this->staff = new ArrayCollection();
		$this->tables = new ArrayCollection();
		$this->menus = new ArrayCollection();
	}


	public function delete()
	{
		parent::delete();
		foreach ($this->getMenus() as $menu)
		{
			$menu->delete();
		}
		foreach ($this->getStaff() as $staff)
		{
			$staff->delete();
		}
		foreach ($this->getTables() as $table)
		{
			$table->delete();
		}
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

	#[Reference('orders')]
	#[CollectionAttribute]
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

	#[Reference('admin')]
	#[CollectionAttribute]
	public function getAdministrators(): Collection
	{
		return $this->getStaff()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('role',[Staff::ROLE_ADMINISTRATOR])
				)
				->andWhere(
					Criteria::expr()->eq('deleted',false)
				)
		);
	}

	#[Reference('not_working_managers')]
	#[CollectionAttribute]
	public function getNotWorkingManagers(): Collection
	{
		return $this->getManagers()->matching(
			Criteria::create()
				->where(Criteria::expr()->in('status',[Staff::STATUS_NOT_WORKING]))
		);
	}

	#[Reference('busy_managers')]
	#[CollectionAttribute]
	public function getBusyManagers(): Collection
	{
		return $this->getManagers()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Staff::STATUS_BUSY])
				)
		);
	}

	#[Reference('free_managers')]
	#[CollectionAttribute]
	public function getFreeManagers() : Collection|Selectable
	{
		return $this->getManagers()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Staff::STATUS_WORKING])
				)
		);
	}

	#[Reference('managers')]
	#[CollectionAttribute]
	public function getManagers(): Collection|Selectable
	{
		return $this->getStaff()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('role',[Staff::ROLE_MANAGER])
				)
				->andWhere(
					Criteria::expr()->eq('deleted',false)
				)
		);
	}

	#[Reference('not_working_stewards')]
	#[CollectionAttribute]
	public function getNotWorkingStewards(): Collection
	{
		return $this->getStewards()->matching(
			Criteria::create()
				->where(Criteria::expr()->in('status',[Staff::STATUS_NOT_WORKING]))
		);
	}

	#[Reference('busy_stewards')]
	#[CollectionAttribute]
	public function getBusyStewards(): Collection
	{
		return $this->getStewards()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Staff::STATUS_BUSY])
				)
		);
	}

	#[Reference('free_stewards')]
	#[CollectionAttribute]
	public function getFreeStewards() : Collection|Selectable
	{
		return $this->getStewards()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Staff::STATUS_WORKING])
				)
		);
	}

	#[Reference('stewards')]
	#[CollectionAttribute]
	public function getStewards(): Collection|Selectable
	{
		return $this->getStaff()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('role',[Staff::ROLE_STAFF])
				)
				->andWhere(
					Criteria::expr()->eq('deleted',false)
				)
		);
	}

	#[Reference('staff')]
	#[CollectionAttribute]
	public function getStaff(): Collection|Selectable
	{
		return $this->staff;
	}

	#[Reference(name: 'current_order')]
	public function getCurrentOrder() : Order|null
	{
		return $this->getOrders()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Order::STATUS_NEW])
				)
		)->first();
	}

	#[Reference(name: 'canceled_orders')]
	public function getCanceledOrders() : Collection
	{
		return $this->getOrders()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Order::STATUS_CANCELED])
				)
		);
	}

	#[Reference(name: 'paid_orders')]
	#[CollectionAttribute]
	public function getPaidOrders() : Collection
	{
		return $this->getOrders()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Order::STATUS_PAID])
				)
		);
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

	#[Reference('tables')]
	#[CollectionAttribute]
	public function getTables(): Collection|Selectable
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
	#[Reference('menus')]
	#[CollectionAttribute]
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
	 * @ORM\PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
	}

	public function getLogo(): Image
	{
		return $this->logo;
	}

	public function setLogo(Image $logo): self
	{
		$this->logo = $logo;

		return $this;
	}
}
