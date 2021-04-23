<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order extends BaseObject
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $status;

	/**
	 * @ORM\Column(type="json")
	 */
	private array $positions = [];

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

	public function __construct()
	{
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
}
