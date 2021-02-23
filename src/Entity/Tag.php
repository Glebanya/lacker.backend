<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="tags")
     */
    private ?Dish $dish;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function getId(): ?Uuid {
        return $this->id;
    }

    public function getDish(): ?Dish {
        return $this->dish;
    }

    public function setDish(?Dish $dish): self {
        $this->dish = $dish;
        return $this;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->getId()->jsonSerialize(),
            'name' => $this->getName()
        ];
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }
}
