<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\MenuRepository;
use App\Types\Lang;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.menu')]
class Menu extends BaseObject
{
	const MENU_TAG_MAIN = "MAIN";
	const MENU_TAG_MINOR = "MINOR";

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('title', getter: 'getTitle', setter: 'setTitle', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $title;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('description', getter: 'getDescription', setter: 'setDescription', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $description;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('restaurant', 'getRestaurant', immutable: true, default: false)]
	#[Assert\NotNull(groups: ["create"])]
	protected ?Restaurant $restaurant;

	/**
	 * @ORM\OneToMany(
	 *     targetEntity=Dish::class,
	 *     mappedBy="menu",
	 *     orphanRemoval=true,
	 *     cascade={"persist"}
	 *	 )
	 */
	#[Assert\Valid(groups: ["create"])]
	#[Assert\Count(min:1, groups: ["create", "update"])]
	#[Field('dishes', 'getDishesFull', immutable: true)]
	protected Collection|Selectable $dishes;

	/**
	 * @ORM\Column(type="string", length=255, options={"default":"MINOR"})
	 */
	#[Assert\Choice([Menu::MENU_TAG_MAIN, Menu::MENU_TAG_MINOR], groups: ["create","update"])]
	#[Assert\NotNull(groups: ["create","update"])]
	#[Field('tag','getTag', 'setTag', immutable: false, default: true)]
	private string $tag;

	public function __construct($params = [])
	{
		$this->dishes = new ArrayCollection();
		if (array_key_exists('description', $params) && is_array($params['description']))
		{
			$this->description = new Lang($params['description']);
		}
		if (array_key_exists('title', $params) && is_array($params['title']))
		{
			$this->title = new Lang($params['title']);
		}
		if (array_key_exists('tag', $params) && is_string($params['tag']))
		{
			$this->tag = $params['tag'];
		}
		if (array_key_exists('dishes', $params) && is_array($params['dishes']))
		{
			foreach ($params['dishes'] as $dish)
			{
				if (is_array($dish))
				{
					$this->addDish(new Dish($dish));
				}
			}
		}
	}

	public function delete()
	{
		parent::delete();
		foreach ($this->getDishes() as $dish)
		{
			$dish->delete();
		}
	}

	public function getTitle(): Lang
	{
		return $this->title;
	}

	public function setTitle(array|Lang $title): self
	{
		$this->title = new Lang($title);

		return $this;
	}

	public function getDescription(): Lang
	{
		return $this->description;
	}

	public function setDescription(array|Lang $description): self
	{
		$this->description = new Lang($description);

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

	#[Reference('dishes')]
	#[CollectionAttribute]
	public function getDishesFull()
	{
		return $this->dishes;
	}

	#[Reference('dishes_valid')]
	#[CollectionAttribute]
	public function getDishes(): Collection|Selectable
	{
		return $this->dishes->matching(
			Criteria::create()
				->where(Criteria::expr()->eq('stopped',false))
		);
	}

	#[Reference('dishes_stoped')]
	#[CollectionAttribute]
	public function getStoppedDishes(): Collection|Selectable
	{
		return $this->dishes->matching(
			Criteria::create()
				->where(Criteria::expr()->eq('stopped',true))
		);
	}

	public function getTag(): ?string
	{
		return $this->tag;
	}

	public function setTag(string $tag): self
	{
		$this->tag = $tag;

		return $this;
	}

	public function addDish(Dish $dish): self
	{
		if (!$this->dishes->contains($dish))
		{
			$this->dishes[] = $dish;
			$dish->setMenu($this);
		}

		return $this;
	}

	public function removeDish(Dish $dish): self
	{
		if ($this->dishes->removeElement($dish))
		{
			if ($dish->getMenu() === $this)
			{
				$dish->setMenu(null);
			}
		}

		return $this;
	}

	#[Assert\Callback(groups: ["create","update"])]
	public function validate(ExecutionContextInterface $context)
	{
		$count = $this->getRestaurant()->getMenus()->matching(
				Criteria::create()
					->where(Criteria::expr()->in('tag',[Menu::MENU_TAG_MAIN]))
					->andWhere(Criteria::expr()->eq('deleted',false))
			)->count();
		if ($count > 1)
		{
			$context->buildViolation("main menu must be single")
				->atPath("restaurant.menu")
				->addViolation()
			;
		}
	}

	/**
	 * @ORM\PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
		$this->getRestaurant()?->onUpdate();
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->getRestaurant()?->onUpdate();
	}
}
