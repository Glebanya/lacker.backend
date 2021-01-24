<?php

namespace App\Entity;

use App\Repository\DishPortionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=DishPortionRepository::class)
 * @ORM\Table(name="`lacker_dish_portion`")
 */
class DishPortion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enable;

    /**
     * @ORM\ManyToOne(targetEntity=dish::class, inversedBy="dishPortions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?dish $dish;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

    public function getDish(): ?dish
    {
        return $this->dish;
    }

    public function setDish(?dish $dish): self
    {
        $this->dish = $dish;

        return $this;
    }
}
