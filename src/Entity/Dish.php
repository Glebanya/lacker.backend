<?php

namespace App\Entity;


use App\Repository\DishRepository;
use App\Types\Lang;
use Doctrine\ORM\Mapping as ORM;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 */
#[ConfiguratorAttribute('app.config.dish')]
class Dish extends BaseObject
{
	/**
	 * @ORM\Column(type="lang")
	 */
	#[Field(name: 'description')]
	#[LangProperty('ru')]
	#[Assert\Valid]
	private Lang $description;

	/**
	 * @ORM\OneToMany(targetEntity=Portion::class, mappedBy="dish", orphanRemoval=true)
	 */
	#[Reference(name: 'portions')]
	#[CollectionAttribute]
	private Collection $portions;

	/**
	 * @ORM\Column(type="lang")
	 */
	#[Field(name: 'name')]
	#[LangProperty('ru')]
	#[Assert\Valid]
	private Lang $name;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="dishes")
	 * @ORM\JoinColumn(nullable=true)
	 */
	#[Reference('restaurant')]
	#[CollectionAttribute]
	private ?Restaurant $restaurant;

	public function __construct($params = [])
	{
		$this->portions = new ArrayCollection();
	}

	public function getDescription(): ?Lang
	{
		return $this->description;
	}

	public function setDescription(array $description): self
	{
		$this->description = new Lang($description);

		return $this;
	}

	/**
	 * @return Collection
	 */
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

	public function setName(array $name): self
	{
		$this->name = new Lang($name);

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

}
