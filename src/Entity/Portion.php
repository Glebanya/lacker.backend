<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PortionRepository;
use App\Types\Price;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PortionRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.portion')]
#[Field('dish', 'getDish', immutable: true)]
class Portion extends BaseObject
{
	/**
	 * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="portions")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create"])]
	protected ?Dish $dish;

	/**
	 * @ORM\Column(type="price")
	 */
	#[Field(name: 'price', getter: 'getPrice', setter: 'setPrice',default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Price $price;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'weight', getter: 'getSize', setter: 'setSize',default: true)]
	#[Assert\PositiveOrZero(groups: ["create", "update"])]
	protected int $weight;

	public function __construct($params = [])
	{
		if (array_key_exists('price', $params) and is_array($params['price']))
		{
			$this->price = new Price($params['price']);
		}
		if (array_key_exists('weight', $params) and is_int($params['weight']))
		{
			$this->weight = $params['weight'];
		}
	}

	#[Reference(name: 'dish')]
	public function getDish(): ?Dish
	{
		return $this->dish;
	}

	public function setDish(?Dish $dish): self
	{
		$this->dish = $dish;

		return $this;
	}

	public function getPrice(): ?Price
	{
		return $this->price;
	}

	public function setPrice(array|Price $price): self
	{
		$this->price = new Price($price);

		return $this;
	}

	public function getSize(): ?int
	{
		return $this->weight;
	}

	public function setSize(int $weight): self
	{
		$this->weight = $weight;
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
		$this->getDish()?->onUpdate();
		$this->getDish()?->getMenu()?->onUpdate();
		$this->getDish()?->getMenu()?->getRestaurant()?->onUpdate();
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->getDish()?->onUpdate();
		$this->getDish()?->getMenu()?->onUpdate();
		$this->getDish()?->getMenu()?->getRestaurant()?->onUpdate();
	}
}
