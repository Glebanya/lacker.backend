<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Immutable;
use App\Configurators\Attributes\Reference;
use App\Repository\UserRepository;
use App\Utils\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use \App\Configurators\Attributes\Collection as AttributeCollection;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ConfiguratorAttribute('app.config.user')]
class User extends BaseUser implements UserInterface, EquatableInterface
{
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	#[Field(name: 'google_id')]
	#[Immutable]
	private ?string $googleId;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $password;

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
	 */
	#[Reference(name: 'orders')]
	#[AttributeCollection]
	private Collection $orders;

	public function __construct($params = [])
	{
		parent::__construct($params);
		$this->orders = new ArrayCollection();
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
			$order->setUser($this);
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
