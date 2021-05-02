<?php

namespace App\Entity;

use App\Configurators\Attributes\Immutable;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BaseObjectRepository;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Configurators\Attributes\Field;

/**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\Entity(repositoryClass=BaseObjectRepository::class)
 * @ORM\DiscriminatorMap({
 *     "dish" = "App\Entity\Dish",
 *     "order" = "App\Entity\Order",
 *     "portion" = "App\Entity\Portion",
 *     "restaurant" = "App\Entity\Restaurant",
 *     "table" = "App\Entity\Table",
 *     "staff" = "App\Entity\Staff",
 *     "user" = "App\Entity\User",
 * })
 * @HasLifecycleCallbacks
 */
abstract class BaseObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="uuid", unique=true)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
	 */
	#[Immutable]
	#[Field(name: 'id')]
	protected ?Uuid $id;

	/**
	 * @ORM\Column(type="datetime")
	 */
	#[Immutable]
	#[Field(name: 'create_data')]
	protected ?DateTimeInterface $crateDate;

	/**
	 * @ORM\Column(type="datetime")
	 */
	#[Immutable]
	#[Field(name: 'update_date')]
	protected ?DateTimeInterface $updateDate;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Immutable]
	#[Field(name: 'update_date')]
	private bool $deleted = false;

	public function getId(): ?Uuid
	{
		return $this->id;
	}

	public function getCrateDate(): ?DateTimeInterface
	{
		return $this->crateDate;
	}

	public function getUpdateDate(): ?DateTimeInterface
	{
		return $this->updateDate;
	}

	public function setUpdateDate(DateTimeInterface $updateDate): self
	{
		$this->updateDate = $updateDate;

		return $this;
	}

	/**
	 * @PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		$this->crateDate = new DateTime('now');
		$this->updateDate = new DateTime('now');
	}

	/**
	 * @PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		$this->updateDate = new DateTime('now');
	}

	public function deleted(): ?bool
	{
		return $this->deleted;
	}

	public function setDeleted(bool $deleted): self
	{
		$this->deleted = $deleted;

		return $this;
	}
}
