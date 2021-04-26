<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection;
use App\Entity\Dish;
use App\Repository\PortionRepository;
use App\Types\Lang;
use App\Types\Price;
use Doctrine\ORM\Mapping as ORM;
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
	#[Assert\Valid]
	private Price $price;

	/**
	 * @ORM\Column(type="lang")
	 */
	#[Field(name: 'size')]
	#[Assert\Valid]
	private Lang $size;

	public function __construct($params = [])
	{
		if (array_key_exists('price', $params) && is_array($params['price']))
		{
			$this->price = new Price($params['price']);
		}
		if (array_key_exists('size', $params) && is_array($params['size']))
		{
			$this->size = new Lang($params['size']);
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

	public function getSize(): ?Lang
	{
		return $this->size;
	}

	public function setSize(array $size): self
	{
		$this->size = new Lang($size);

		return $this;
	}
}
