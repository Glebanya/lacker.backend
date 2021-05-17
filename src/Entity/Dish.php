<?php

namespace App\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DishRepository;
use App\Types\Image;
use App\Types\Lang;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.dish')]
class Dish extends BaseObject
{
	public const TYPE_ALCOHOL = 'ALCOHOL', TYPE_DISH = 'DISH', TYPE_DRINKS = 'DRINKS';

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'description', getter: 'getDescription', setter: 'setDescription', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $description;
	/**
	 * @ORM\OneToMany(
	 *     targetEntity=Portion::class,
	 *     mappedBy="dish",
	 *     orphanRemoval=true,
	 *     cascade={"persist"}
	 *)
	 */
	#[Assert\Count(
		min: 1,
		max: 10,
		minMessage: "portions count must be more than {{ limit }}",
		maxMessage: "portions count must be less than {{ limit }}",
		groups: ["create", "update"]
	)]
	#[Assert\Valid(groups: ["create", "update"])]
	#[Field('portions', 'getPortions', immutable: true, default: true)]
	protected Collection $portions;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'title',getter: 'getName',setter: 'setName', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $name;

	/**
	 * @ORM\Column(type="image", nullable=true)
	 */
	#[Field(name: 'image', getter: 'getImage', setter:'setImage', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Image $image;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	#[Field(name: 'type', getter: 'getType', setter: 'setType', default: true)]
	#[Assert\Choice([Dish::TYPE_ALCOHOL, Dish::TYPE_DISH, Dish::TYPE_DRINKS], groups: ["create", "update"])]
	protected ?string $type;

	/**
	 * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="dishes")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('menu', 'getMenu', immutable: true, default: false)]
	protected ?Menu $menu;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Field(name: 'stopped', getter: 'isStopped', setter:'setStopped', default: true)]
	private bool $stopped;

	public function isStopped(): ?bool
	{
		return $this->stopped;
	}

	public function setStopped(bool $stopped): self
	{
		$this->stopped = $stopped;
		return $this;
	}

	public function __construct($params = [])
	{
		$this->portions = new ArrayCollection();
		$this->stopped = false;
		if (array_key_exists('description', $params) && is_array($params['description']))
		{
			$this->description = new Lang($params['description']);
		}
		if (array_key_exists('title', $params) && is_array($params['title']))
		{
			$this->name = new Lang($params['title']);
		}
		if (array_key_exists('type', $params) && is_string($params['type']))
		{
			$this->type = $params['type'];
		}
		if (array_key_exists('image', $params) && is_string($params['image']))
		{
			$this->image = new Image($params['image']);
		}
		if (array_key_exists('portions', $params) && is_array($params['portions']))
		{
			foreach ($params['portions'] as $portion)
			{
				if (is_array($portion))
				{
					$this->addPortion(new Portion($portion));
				}
			}
		}
	}

	public function delete()
	{
		parent::delete();
		foreach ($this->getPortions() as $portion)
		{
			$portion->delete();
		}
	}

	public function getDescription(): ?Lang
	{
		return $this->description;
	}

	public function setDescription(array|Lang $description): self
	{
		$this->description = new Lang($description);

		return $this;
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
			$portion->setDish($this);
		}

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

		return $this;
	}

	public function getName(): ?Lang
	{
		return $this->name;
	}

	public function setName(array|Lang $name): self
	{
		$this->name = new Lang($name);

		return $this;
	}

	public function getImage(): Image
	{
		return $this->image;
	}

	public function setImage($image): self
	{
		$this->image = $image;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	#[Reference(name: "menu")]
	public function getMenu(): ?Menu
	{
		return $this->menu;
	}

	public function setMenu(?Menu $menu): self
	{
		$this->menu = $menu;

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
		$this->getMenu()?->onUpdate($eventArgs);
		$this->getMenu()?->getRestaurant()?->onUpdate($eventArgs);
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->getMenu()?->onUpdate();
		$this->getMenu()?->getRestaurant()?->onUpdate();
	}
}
