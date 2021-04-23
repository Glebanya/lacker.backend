<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Utils\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends BaseUser
{
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private ?string $googleId;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $password;

	/**
	 * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
	 */
	private Collection $orders;

	public function __construct()
	{
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

	#[Pure] public function getSalt(): ?string
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
}
