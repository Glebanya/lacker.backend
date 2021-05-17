<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use Doctrine\ORM\Event\LifecycleEventArgs;
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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.order')]
#[Field('restaurant', getter: 'getRestaurant', immutable: true,  default: true)]
#[Field('portions', getter: 'getPortions', immutable: true, default: true)]
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
	 * @ORM\Column(type="text", nullable=true)
	 */
	#[Field('comment', getter: 'getComment', setter: 'setComment',default: true)]
	protected ?string $comment;

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
	 * @ORM\ManyToMany(targetEntity=Portion::class, fetch="EXTRA_LAZY")
	 */
	protected Collection $portions;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	protected ?string $currency;

	/**
	 * @ORM\Column(type="price")
	 */
	#[Field('sum', getter: 'getSum')]
	#[Assert\Valid(groups: ["create"])]
	protected Price $sum;

	public function __construct(array $params = [])
	{
		$this->portions = new ArrayCollection();
		$this->sum = new Price();
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

	public function getSum(): Price
	{
		return $this->sum;
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

	#[Reference(name: 'portions')]
	#[CollectionAttribute]
	public function getPortions(): Collection
	{
		return $this->portions;
	}

	public function addPortion(Portion $portion): self
	{
		if (!$this->portions->contains($portion))
		{
			$this->portions[] = $portion;
		}
		$this->calculateSum();
		return $this;
	}

	public function removePortion(Portion $portion): self
	{
		if ($this->portions->removeElement($portion))
		{
			if ($portion->getDish() === $this)
			{
				$portion->setDish(null);
			}
		}
		$this->calculateSum();
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
		if ($eventArgs && $eventArgs->hasChangedField('portions'))
		{
			$this->calculateSum();
		}
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->calculateSum();
	}

	#[Assert\Callback(groups: ['update','create'])]
	public function validate(ExecutionContextInterface $context)
	{
		if ($this->getPortions()->count() === 0)
		{
			$context->buildViolation("no portions")->atPath("portions")->addViolation();
		}
		else
		{
			$restaurantId = $this->getRestaurant()->getId();
			foreach ($this->getPortions() as $portion)
			{
				$portionId = $portion->getDish()->getMenu()->getRestaurant()->getId();
				if (!$portionId->equals($restaurantId))
				{
					$context->buildViolation("error wrong portion")->atPath("portions")->addViolation();
				}
			}
		}
	}

}
