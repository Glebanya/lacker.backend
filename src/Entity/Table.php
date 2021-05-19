<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\TableRepository;
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
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 * @ORM\HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.table')]
#[Field('restaurant', 'getRestaurant', immutable: true)]
class Table extends BaseObject
{
	public const STATUS_FREE = "FREE", STATUS_BUSY = "BUSY", STATUS_RESERVED = "RESERVED";
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'status',getter: 'getStatus',setter: 'setStatus', default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Assert\Choice([Table::STATUS_FREE, Table::STATUS_BUSY, Table::STATUS_RESERVED], groups: ["create", "update"])]
	protected string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="tables")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create", "update"])]
	protected Restaurant $restaurant;

	/**
	 * @ORM\Column(type="integer")
	 */
	#[Field(name: 'persons' , getter: 'getPersons', setter: 'setPersons', default: true)]
	#[Assert\Positive(groups: ["create", "update"])]
	#[Assert\NotNull(groups: ["create", "update"])]
	protected int $persons;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'title',getter: 'getTitle',setter: 'setTitle', default: true)]
	#[Assert\Valid(groups: ["create", "update"])]
	protected Lang $title;

	/**
	 * @ORM\OneToMany(targetEntity=TableReserve::class, mappedBy="ReservedTable", orphanRemoval=true, fetch="EXTRA_LAZY")
	 */
	protected Collection|Selectable $tableReserves;

	/**
	 * Table constructor.
	 *
	 * @param array $params
	 */
	public function __construct($params = [])
	{
		if (array_key_exists('status', $params) && is_string($params['status']))
		{
			$this->status = $params['status'];
		}
		if (array_key_exists('persons', $params) && is_int($params['persons']))
		{
			$this->persons = $params['persons'];
		}
		if (array_key_exists('title', $params) && is_array($params['title']))
		{
			$this->title = new Lang($params['title']);
		}
		$this->tableReserves = new ArrayCollection();
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

	public function getPersons(): ?string
	{
		return $this->persons;
	}

	public function setPersons($persons): self
	{
		$this->persons = intval($persons);

		return $this;
	}

	public function setTitle(Lang|array $lang) : self
	{
		$this->title = new Lang($lang);
		return $this;
	}

	public function getTitle() : Lang
	{
		return $this->title;
	}

	#[Reference('restaurant')]
	public function getRestaurant(): Restaurant
	{
		return $this->restaurant;
	}

	public function setRestaurant(Restaurant $restaurant): self
	{
		$this->restaurant = $restaurant;

		return $this;
	}

	public function getCurrentReserve() : TableReserve
	{
		return $this->getTableReserves()->matching(
			Criteria::create()
				->where(
					Criteria::expr()->in('status',[TableReserve::STATUS_NEW])
				)
		)->first();
	}

	#[Reference('table_reserve')]
	#[CollectionAttribute]
	public function getTableReserves(): Collection|Selectable
	{
		return $this->tableReserves;
	}

	public function addTableReserve(TableReserve $tableReserve): self
	{
		if (!$this->tableReserves->contains($tableReserve))
		{
			$this->tableReserves[] = $tableReserve;
			$tableReserve->setReservedTable($this);
		}

		return $this;
	}

	public function removeTableReserve(TableReserve $tableReserve): self
	{
		if ($this->tableReserves->removeElement($tableReserve))
		{
			if ($tableReserve->getReservedTable() === $this)
			{
				$tableReserve->setReservedTable(null);
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
		$this->status = static::STATUS_FREE;
	}
}
