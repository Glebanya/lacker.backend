<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use Symfony\Component\Validator\Constraints as Assert;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
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
#[ConfiguratorAttribute('app.config.order')]
class Order extends BaseObject
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field('status')]
	#[Assert\Choice(['PAID','NEW','CANCELED'], groups: ["creation"])]
	private ?string $status;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	#[Field('comment')]
	private ?string $comment;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('user')]
	#[Assert\NotNull(groups: ["create"])]
	private ?User $user;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="orders")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('restaurant')]
	#[Assert\NotNull(groups: ["create"])]
	private ?Restaurant $restaurant;

	/**
	 * @ORM\ManyToMany(targetEntity=Portion::class)
	 */
	#[Reference('portion')]
	#[CollectionAttribute]
	private Collection $portions;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private ?string $currency;

	/**
	 * @ORM\Column(type="price")
	 */
	#[Field('price')]
	#[Assert\Valid(groups: ["create"])]
	private Price $sum;

	public function __construct(array $params = [])
	{
		$this->portions = new ArrayCollection();
		if (array_key_exists('comment', $params) && is_string($params['comment']))
		{
			$this->comment = $params['comment'];
		}
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
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
		if ($eventArgs && $eventArgs->hasChangedField('portions'))
		{
			$this->calculateSum();
		}
	}

	/**
	 * @PrePersist
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onAdd(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->calculateSum();
	}


}
