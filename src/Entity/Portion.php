<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Dish;
use App\Repository\PortionRepository;
use App\Types\Lang;
use App\Types\Price;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PortionRepository::class)
 */
#[ConfiguratorAttribute('app.config.portion')]
class Portion extends BaseObject
{

	/**
	 * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="portions")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference(name: 'dish')]
	#[Collection]
	private ?Dish $dish;

	/**
	 * @ORM\Column(type="price")
	 */
	#[Field(name: 'price')]
	#[Assert\Valid()]
	private Price $price;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'weight')]
	#[Assert\PositiveOrZero]
	private int $weight;

	public function __construct($params = [])
	{
		if (array_key_exists('price', $params) && is_array($params['price']))
		{
			$this->price = new Price($params['price']);
		}
		if (array_key_exists('weight', $params) && is_int($params['weight']))
		{
			$this->weight = $params['weight'];
		}
	}

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

	public function setPrice(array $price): self
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
}
