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
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.menu')]
class Menu extends BaseObject
{
	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('title')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $title;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field('description')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $description;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="menus")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('restaurant')]
	private ?Restaurant $restaurant;

	/**
	 * @ORM\OneToMany(targetEntity=Dish::class, mappedBy="menu", orphanRemoval=true)
	 * @Assert\All({
	 *      @Assert\Type("\App\Entity\Dish")
	 * })
	 */
	#[Reference('dishes')]
	#[CollectionAttribute]
	#[Assert\Valid]
	private Collection $dishes;

	public function __construct($params = [])
	{
		$this->dishes = new ArrayCollection();
		if (array_key_exists('description', $params) && is_array($params['description']))
		{
			$this->description = new Lang($params['description']);
		}
		if (array_key_exists('title', $params) && is_array($params['name']))
		{
			$this->title = new Lang($params['name']);
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

	public function getTitle(): Lang
	{
		return $this->title;
	}

	public function setTitle(array $title): self
	{
		$this->title = new Lang($title);

		return $this;
	}

	public function getDescription(): Lang
	{
		return $this->description;
	}

	public function setDescription($description): self
	{
		$this->description = $description;

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

	/**
	 * @return Collection
	 */
	public function getDishes(): Collection
	{
		return $this->dishes;
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
	 * @PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
	}

	/**
	 * @PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
	}
}
