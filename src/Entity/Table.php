<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\TableRepository;
use App\Types\Lang;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 * @HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.table')]
class Table extends BaseObject
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'status')]
	#[Assert\NotNull( groups: ["create", "update"])]
	#[Assert\Choice(["BUSY","FREE","RESERVED"],groups: ["create", "update"])]
	private string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="tables")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('restaurant')]
	#[Assert\NotNull(groups: ["create", "update"])]
	private Restaurant $restaurant;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'persons')]
	#[Assert\Positive(groups: ["create", "update"])]
	#[Assert\NotNull(groups: ["create", "update"])]
	private int $persons;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'title')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $title;

	/**
	 * Table constructor.
	 *
	 * @param array $params
	 */
	public function __construct($params = [])
	{
		if (array_key_exists('persons', $params) && is_int($params['persons']))
		{
			$this->persons = $params['persons'];
		}
		if (array_key_exists('title', $params) && is_array($params['title']))
		{
			$this->title = new Lang($params['title']);
		}
	}

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
