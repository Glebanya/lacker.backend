<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Constraints as Assert;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.order')]
class Order extends BaseObject
{
	public const STATUS_PAID = 'PAID', STATUS_NEW = 'NEW', STATUS_CANCELED = 'CANCELED';

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field('status', getter: 'getStatus', setter: 'setStatus', default: true)]
	#[Assert\Choice([Order::STATUS_PAID, Order::STATUS_NEW, Order::STATUS_CANCELED], groups: ["create"])]
	protected ?string $status;
	public function getStatus(): ?string
	{
		return $this->status;
	}
	public function setStatus(string $status): self
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create"])]
	#[Field('user', getter: 'getUser', immutable: true, default: true)]
	protected ?User $user;
	public function setUser(?User $user): self
	{
		$this->user = $user;
		return $this;
	}
	#[Reference('user')]
	public function getUser(): ?User
	{
		return $this->user;
	}
	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create"])]
	#[Field('restaurant', getter: 'getRestaurant', immutable: true,  default: true)]
	protected ?Restaurant $restaurant;

	/**
	* @ORM\OneToMany(targetEntity=SubOrder::class, mappedBy="baseOrder", orphanRemoval=true, cascade={"persist"})
	*/
	#[Field('sub_orders', getter: 'getSubOrders', immutable: true, default: false)]
	private Collection|Selectable $subOrders;

	/**
	* @ORM\Column(type="integer")
	*/
	#[Field('count', getter: 'getFinalCount', immutable: true, default: true)]
	private ?int $finalCount;
	public function getFinalCount(): ?int
	{
		return $this->finalCount;
	}
	/**
	 * @ORM\ManyToOne(targetEntity=Table::class)
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create"])]
	#[Field('table', getter: 'getTable', immutable: true, default: true)]
	private ?Table $orderTable;

	 /**
	  * @ORM\Column(type="boolean")
	  */
	#[Field('checked', getter: 'getChecked', immutable: true, default: true)]
 	private bool $checked;
	public function getChecked(): ?bool
	{
		return $this->checked;
	}
	public function setChecked(bool $checked): self
	{
		$this->checked = $checked;
		return $this;
	}

	public function __construct(Table $table, User $user)
	{
		$this->setTable($table)
			->setRestaurant($table->getRestaurant())
			->setStatus(Order::STATUS_NEW)
			->setUser($user)
			->subOrders = new ArrayCollection();
	}

	#[Reference('restaurant')]
	public function getRestaurant(): ?Restaurant
	{
		return $this->restaurant;
	}

	public function setRestaurant(?Restaurant $restaurant): self
	{
		$this->restaurant = $restaurant;

		return $this;
	}

	#[Reference('table')]
	public function getTable(): ?Table
	{
		return $this->orderTable;
	}

	public function setTable(?Table $table): self
	{
		$this->orderTable = $table;

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
		$this->count();
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->count();
	}

	/**
	* @return Collection|Selectable
	*/
	public function getSubOrders(): Collection|Selectable
	{
		return $this->subOrders;
	}

	public function addSubOrder(SubOrder $subOrder): SubOrder
	{
		if (!$this->subOrders->contains($subOrder))
		{
			$this->subOrders[] = $subOrder;
			$subOrder->setBaseOrder($this);
		}

		return $subOrder;
	}

	public function removeSubOrder(SubOrder $subOrder): self
	{
		if ($this->subOrders->removeElement($subOrder))
		{
			if ($subOrder->getBaseOrder() === $this)
			{
				$subOrder->setBaseOrder(null);
			}
		}

		return $this;
	}

	public function count()
	{
		$this->finalCount = 0;
		foreach ($this->getSubOrders() as $subOrder)
		{
			$this->finalCount += $subOrder->getCount();
		}
	}


}
