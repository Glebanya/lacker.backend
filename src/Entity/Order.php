<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Types\Price;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @HasLifecycleCallbacks
 */
class Order extends BaseObject
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $status;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private ?string $comment;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private ?User $user;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private ?Restaurant $restaurant;

	/**
	 * @ORM\ManyToMany(targetEntity=Portion::class)
	 */
	private Collection $portions;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $currency;

	/**
	 * @ORM\Column(type="price")
	 */
	private Price $sum;

	public function __construct()
	{
		$this->portions = new ArrayCollection();
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

	public function getComment(): ?string
	{
		return $this->comment;
	}

	public function setComment(?string $comment): self
	{
		$this->comment = $comment;

		return $this;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

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

	/**
	 * @PrePersist
	 */
	public function onAdd()
	{
		parent::onAdd();
	}

	public function calculateSum()
	{
		foreach ($this->portions as $portion)
		{
			foreach ($portion->getPrice() as $currency => $value)
			{
				$this->sum[$currency] = $this->sum[$currency] ?? 0 + $value;
			}
		}
	}
	/**
	 * @PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $event
	 */
	public function onUpdate(PreUpdateEventArgs $event = null)
	{
		parent::onUpdate();
		if ($event)
		{
			if ($event->hasChangedField('portions'))
			{
				$this->calculateSum();
			}
		}
	}


}
