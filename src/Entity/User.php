<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as AttributeCollection;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Immutable;
use App\Configurators\Attributes\Reference;
use App\Repository\UserRepository;
use App\Utils\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ConfiguratorAttribute('app.config.user')]
class User extends BaseUser implements UserInterface, EquatableInterface
{
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	#[Field(name: 'google_id',getter: 'getGoogleId')]
	#[Immutable]
	protected ?string $googleId;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	protected ?string $password;

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user", fetch="EXTRA_LAZY")
	 */
	protected Collection|Selectable $orders;

	/**
	 * @ORM\OneToMany(targetEntity=TableReserve::class, mappedBy="user", orphanRemoval=true, fetch="EXTRA_LAZY")
	 */
	protected Collection|Selectable $tableReserves;


	public function __construct($params = [])
	{
		parent::__construct($params);
		$this->orders = new ArrayCollection();
		$this->tableReserves = new ArrayCollection();
	}

	public function getGoogleId(): ?string
	{
		return $this->googleId;
	}

	public function setGoogleId(string $googleId): self
	{
		$this->googleId = $googleId;

		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;

		return $this;
	}

	public function getRoles(): ?array
	{
		return ['ROLE_CLIENT'];
	}

	public function getSalt(): ?string
	{
		return Environment::get('USER_SALT');
	}

	public function eraseCredentials()
	{
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
	#[AttributeCollection]
	public function getPaidOrders() : Collection
	{
		return $this->getOrders()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[Order::STATUS_PAID])
				)
		);
	}

	#[Reference(name: 'orders')]
	#[AttributeCollection]
	public function getOrders(): Collection|Selectable
	{
		return $this->orders;
	}

	public function addOrder(Order $order): self
	{
		if (!$this->orders->contains($order))
		{
			$this->orders[] = $order;
			$order->setUser($this);
		}

		return $this;
	}

	#[Reference('reserved_tables')]
	#[CollectionAttribute]
	public function getTableReserves(): Collection|Selectable
	{
		return $this->tableReserves;
	}

	#[Reference('current_reserve')]
	public function getCurrentTable() : TableReserve|false|null
	{
		return $this->getTableReserves()->matching(
			Criteria::create()
				->where(Criteria::expr()->in('status',[TableReserve::STATUS_NEW]))
				->andWhere(Criteria::expr()->eq('deleted',false))
		)->first();
	}

	public function addTableReserve(TableReserve $tableReserve): self
	{
		if (!$this->tableReserves->contains($tableReserve))
		{
			$this->tableReserves[] = $tableReserve;
			$tableReserve->setUser($this);
		}

		return $this;
	}

	public function removeTableReserve(TableReserve $tableReserve): self
	{
		if ($this->tableReserves->removeElement($tableReserve))
		{
			// set the owning side to null (unless already changed)
			if ($tableReserve->getUser() === $this)
			{
				$tableReserve->setUser(null);
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

}
