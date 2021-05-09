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

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.menu')]
class Menu extends BaseObject
{
	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('title', getter: 'getTitle', setter: 'setTitle')]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $title;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('description', getter: 'getDescription', setter: 'setDescription')]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $description;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
	 * @ORM\JoinColumn(nullable=false)
	 */
	protected ?Restaurant $restaurant;

	/**
	 * @ORM\OneToMany(
	 *     targetEntity=Dish::class,
	 *     mappedBy="menu",
	 *     orphanRemoval=true,
	 *     fetch="EXTRA_LAZY",
	 *     cascade={"persist"}
	 *	 )
	 * @Assert\All({
	 *      @Assert\Type("\App\Entity\Dish")
	 * })
	 */
	#[Assert\Valid]
	protected Collection|Selectable $dishes;

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
		if (array_key_exists('dishes', $params) && is_array($params['dishes']))
		{
			foreach ($params['dishes'] as $dish)
			{
				if (is_array($dish))
				{
					$this->dishes->add(new Dish($dish));
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
	public function getDishes(): Collection|Selectable
	{
		return $this->dishes;
	}

	#[Reference('drinks')]
	#[CollectionAttribute]
	public function getDrinks() : Collection
	{
		return $this->getDishes()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('type',[Dish::TYPE_DRINKS])
				)
		);
	}

	#[Reference('alcohol')]
	#[CollectionAttribute]
	public function getAlcohol() : Collection
	{
		return $this->getDishes()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('type',[Dish::TYPE_ALCOHOL])
				)
		);
	}

	#[Reference('dish')]
	#[CollectionAttribute]
	public function getDish() : Collection
	{
		return $this->getDishes()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('type',[Dish::TYPE_DISH])
				)
		);
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

	/**
	 * @ORM\PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
	}
}
