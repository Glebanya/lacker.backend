<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
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
}
