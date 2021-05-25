<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\PortionRepository;
use App\Types\Lang;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
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
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'price', getter: 'getPrice', setter: 'setPrice', default: true)]
	#[Assert\PositiveOrZero(groups: ["create", "update"])]
	#[Assert\NotNull(groups: ["create", "update"])]
	protected ?int $price = 0;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'weight', getter: 'getSize', setter: 'setSize', default: true)]
	#[Assert\PositiveOrZero(groups: ["create", "update"])]
	#[Assert\NotNull(groups: ["create", "update"])]
	protected ?int $weight = 0;

	/**
	 * @ORM\Column(type="lang_phrase", nullable=true)
	 */
	#[Field(name: 'title', getter: 'getTitle', setter: 'setTitle', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	private ?Lang $title = null;

	/**
	 * @ORM\Column(type="integer", options={ "default": 0 })
	 */
	#[Field(name: 'sort', getter: 'getSort', setter: 'setSort', default: true)]
	#[Assert\PositiveOrZero(groups: ["create", "update"])]
	private ?int $sort = 0;

	public function __construct($params = [])
	{
		if (array_key_exists('price', $params) and is_int($params['price']))
		{
			$this->price = ($params['price']);
		}
		if (array_key_exists('weight', $params) and is_int($params['weight']))
		{
			$this->weight = $params['weight'];
		}
		if (array_key_exists('title', $params) and is_array($params['title']))
		{
			$this->title = new Lang($params['title']);
		}
		if (array_key_exists('sort', $params) and is_int($params['sort']))
		{
			$this->sort = $params['sort'];
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

	public function getPrice(): ?int
	{
		return $this->price;
	}

	public function setPrice(int $price): self
	{
		$this->price = $price;
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

	public function getTitle(): Lang
	{
		return $this->title;
	}

	public function setTitle(Lang $title): self
	{
		$this->title = $title;

		return $this;
	}

	public function getSort(): ?int
	{
		return $this->sort;
	}

	public function setSort(int $sort): self
	{
		$this->sort = $sort;

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
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd();
		$this->getDish()?->onUpdate();
	}
}
