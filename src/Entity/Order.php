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
#[Field('restaurant', getter: 'getRestaurant', immutable: true,  default: true)]
#[Field('sub_orders', getter: 'getSubOrders', immutable: true, default: false)]
#[Field('user', getter: 'getUser', immutable: true, default: true)]
class Order extends BaseObject
{
	public const STATUS_PAID = 'PAID', STATUS_NEW = 'NEW', STATUS_CANCELED = 'CANCELED';

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field('status', getter: 'getStatus', setter: 'setStatus', default: true)]
	#[Assert\Choice([Order::STATUS_PAID, Order::STATUS_NEW, Order::STATUS_CANCELED], groups: ["create"])]
	protected ?string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */

	#[Assert\NotNull(groups: ["create"])]
	protected ?User $user;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create"])]
	protected ?Restaurant $restaurant;

	/**
	* @ORM\OneToMany(targetEntity=SubOrder::class, mappedBy="baseOrder", orphanRemoval=true, cascade={"persist"})
	*/
	private Collection|Selectable $subOrders;

	/**
	* @ORM\Column(type="integer")
	*/
	private ?int $finalCount;

	public function __construct(Restaurant $restaurant)
	{
		$this->restaurant = $restaurant;
		$this->subOrders = new ArrayCollection();
		$this->status = Order::STATUS_NEW;
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	#[Reference('user')]
	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
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

	public function getFinalCount(): ?int
	{
		return $this->finalCount;
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
