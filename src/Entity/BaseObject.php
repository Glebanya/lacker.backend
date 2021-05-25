<?php

namespace App\Entity;

use App\Configurators\Attributes\Field;
use App\Repository\BaseObjectRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\Entity(repositoryClass=BaseObjectRepository::class)
 * @ORM\DiscriminatorMap({
 *     "dish" = "App\Entity\Dish",
 *     "order" = "App\Entity\Order",
 *     "portion" = "App\Entity\Portion",
 *     "restaurant" = "App\Entity\Restaurant",
 *     "menu" = "App\Entity\Menu",
 *     "staff" = "App\Entity\Staff",
 *     "user" = "App\Entity\User",
 *     "table" = "App\Entity\Table",
 *     "reserve" = "App\Entity\TableReserve",
 *     "user_base" = "App\Entity\BaseUser",
 *     "suborder" = "App\Entity\SubOrder",
 *     "appeal" = "App\Entity\Appeal",
 * })
 * @ORM\HasLifecycleCallbacks
 */
abstract class BaseObject
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="uuid", unique=true)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
	 */
	#[Field(name: 'id', getter: 'getId', immutable: true, default: true)]
	protected ?Uuid $id;

	/**
	 * @ORM\Column(type="datetime")
	 */
	#[Field(name: 'create_time', getter: 'getCrateDate', immutable: true, default: true)]
	protected ?DateTimeInterface $crateDate;

	/**
	 * @ORM\Column(type="datetime")
	 */
	#[Field(name: 'update_time', getter: 'getUpdateDate', immutable: true, default: true)]
	protected ?DateTimeInterface $updateDate;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Field(name: 'delete', getter: 'isDeleted', immutable: true)]
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
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		$this->crateDate = new DateTime('now');
		$this->updateDate = new DateTime('now');
		$this->deleted = false;
	}

	/**
	 * @ORM\PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		$this->updateDate = new DateTime('now');
	}

	public function isDeleted(): ?bool
	{
		return $this->deleted;
	}

	public function delete()
	{
		$this->deleted = true;
	}
}
