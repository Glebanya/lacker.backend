<?php

namespace App\Entity;

use DateTime;
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
 *     "table" = "App\Entity\Table"
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
    #[Field(name: 'id')]
    protected ?Uuid $id;


    /**
     * @ORM\Column(type="datetime")
     */
    #[Field(name: 'create_data')]
    protected ?\DateTimeInterface $crateDate;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Field(name: 'update_date')]
    protected ?\DateTimeInterface $updateDate;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCrateDate(): ?\DateTimeInterface
    {
        return $this->crateDate;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

	/**
	 * @PrePersist
	 */
	public function onAdd()
	{
		$this->crateDate = new DateTime('now');
		$this->updateDate = new DateTime('now');
	}

	/**
	 * @PreUpdate
	 */
	public function onUpdate()
	{
		$this->updateDate = new DateTime('now');
	}
}
