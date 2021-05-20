<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\SubOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity(repositoryClass=SubOrderRepository::class)
* @ORM\HasLifecycleCallbacks
*/
#[ConfiguratorAttribute('app.config.suborder')]
class SubOrder extends BaseObject
{
	/**
	 * @ORM\ManyToMany(targetEntity=Portion::class)
	 */
	#[Field('portions','getPortions', immutable: true)]
	private Collection|Selectable $portions;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Field('checked','setChecked','setChecked')]
	private ?bool $checked = false;

	/**
	* @ORM\ManyToOne(targetEntity=Order::class, inversedBy="subOrders")
	* @ORM\JoinColumn(nullable=false)
	*/
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Field('order','getBaseOrder', immutable: true)]
	private ?Order $baseOrder;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Field('count','getCount', immutable: true)]
	private ?int $count;

	public function __construct(
		Order $order,
		iterable $portions = [],
		bool $checked = false,
	)
	{
		$this->portions = new ArrayCollection();
		$order->addSubOrder($this);
		$this->checked = $checked;
		foreach ($portions as $portion)
		{
			$this->addPortion($portion);
		}
		$this->count();
	}

	/**
	 * @return Collection|Selectable
	 */
	#[Reference('portions')]
	#[CollectionAttribute]
	public function getPortions():  Collection|Selectable
	{
		return $this->portions;
	}

	public function addPortion(Portion $portion): self
	{
		if (!$this->portions->contains($portion))
		{
			$this->portions[] = $portion;
		}
		return $this;
	}

	public function removePortion(Portion $portion) : self
	{
		$this->portions->remove($portion);
		return $this;
	}

	public function getChecked(): ?bool
	{
		return $this->checked;
	}

	public function setChecked(bool $checked): self
	{
		$this->checked = $checked;

		return $this;
	}

	#[Reference('order')]
	public function getBaseOrder(): ?Order
	{
		return $this->baseOrder;
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
			$restaurantId = $this->getBaseOrder()->getRestaurant()->getId();
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

	public function setBaseOrder(?Order $baseOrder): self
	{
		$this->baseOrder = $baseOrder;
		return $this;
	}

	public function getCount(): ?int
	{
		return $this->count;
	}

	public function count()
	{
		$this->count = 0;
		foreach ($this->getPortions() as $portion)
		{
			$this->count += $portion->getPrice();
		}
	}

	/**
	 * @ORM\PrePersist
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->count();
	}

	/**
	 * @ORM\PreUpdate
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
		$this->count();
	}
}
