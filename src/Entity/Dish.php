<?php

namespace App\Entity;

use Doctrine\Common\Collections\Selectable;
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
	public const TYPE_ALCOHOL = 'ALCOHOL', TYPE_DRINKS = 'DRINKS';
	public const TYPE_BIRD_DISH = 'BIRD', TYPE_SEA_DISH = 'SEA', TYPE_MEAT_DISH = 'MEAT';
	public const TYPE_GARNISH = 'GARNISH', TYPE_ASIAN_DISH = 'ASIAN_DISH', TYPE_DESSERT = 'DESSERT';
	public const TYPE_SALAD = 'SALAD', TYPE_SANDWICH = 'SANDWICH', TYPE_SOUP = 'SOUP';
	public const TYPE_OTHER = 'OTHER';

	public static function getTypes() : array
	{
		return [
			Dish::TYPE_ALCOHOL,
			Dish::TYPE_DRINKS,
			Dish::TYPE_BIRD_DISH,
			Dish::TYPE_SEA_DISH,
			Dish::TYPE_MEAT_DISH,
			Dish::TYPE_GARNISH,
			Dish::TYPE_ASIAN_DISH,
			Dish::TYPE_DESSERT,
			Dish::TYPE_SALAD,
			Dish::TYPE_SANDWICH,
			Dish::TYPE_SOUP,
			Dish::TYPE_OTHER
		];
	}

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
	#[Assert\Count(min:1,groups: ["create", "update"])]
	#[Field('portions', 'getPortions', immutable: true, default: true)]
	protected Collection|Selectable $portions;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'title',getter: 'getName',setter: 'setName', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected ?Lang $name;

	/**
	 * @ORM\Column(type="image", nullable=true)
	 */
	#[Field(name: 'image', getter: 'getImage', setter:'setImage', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected ?Image $image;

	/**
	 * @ORM\Column(type="simple_array", length=255, nullable=true)
	 * @Assert\All({
	 * 			@Assert\Choice(callback={Dish::class,"getTypes"}, groups={"create","update"})
	 * 		},
	 *	 groups={"create","update"}
	 *)
	 */
	#[Field(name: 'tags', getter: 'getType', setter: 'setType', default: true)]
	#[Assert\Count( min:1, groups: ["create", "update"])]
	protected ?array $type;

	/**
	 * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="dishes")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('menu', 'getMenu', immutable: true, default: false)]
	#[Assert\NotNull(groups:["create"])]
	protected ?Menu $menu;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Field(name: 'stopped', getter: 'isStopped', setter:'setStopped', default: true)]
	#[Assert\NotNull(groups:["create"])]
	private bool $stopped = false;

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
		if (array_key_exists('tags', $params) && is_array($params['tags']))
		{
			$this->type = $params['tags'];
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

	public function getType(): ?array
	{
		return $this->type;
	}

	public function setType(?array $type): self
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
