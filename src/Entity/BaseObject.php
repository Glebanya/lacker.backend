<?php

namespace App\Entity;

use App\API\Attributes\Field;
use App\Repository\BaseObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;
/**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\Entity(repositoryClass=BaseObjectRepository::class)
 * @ORM\DiscriminatorMap({"base" = "App\Entity\Base", "kek" = "App\Entity\Kek"})
 */
class BaseObject
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    #[Field(name: 'id',default: true)]
    protected ?Uuid $id;


    /**
     * @ORM\Column(type="datetime")
     */
    #[Field(name: 'create_data',default: true)]
    protected ?\DateTimeInterface $crateDate;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Field(name: 'update_date',default: true)]
    protected ?\DateTimeInterface $updateDate;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCrateDate(): ?\DateTimeInterface
    {
        return $this->crateDate;
    }

    public function setCrateDate(\DateTimeInterface $crateDate): self
    {
        $this->crateDate = $crateDate;

        return $this;
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
}
