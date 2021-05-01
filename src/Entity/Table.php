<?php

namespace App\Entity;

use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\LangProperty;
use App\Configurators\Attributes\Reference;
use App\Repository\TableRepository;
use App\Types\Lang;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 */
class Table extends BaseObject
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'status')]
	private string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="tables")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('restaurant')]
	private Restaurant $restaurant;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'persons')]
	private int $persons;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'title')]
	#[LangProperty('ru')]
	private Lang $title;


	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	public function getRestaurant(): Restaurant
	{
		return $this->restaurant;
	}

	public function setRestaurant(Restaurant $restaurant): self
	{
		$this->restaurant = $restaurant;

		return $this;
	}

}
