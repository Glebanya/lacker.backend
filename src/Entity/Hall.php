<?php

namespace App\Entity;

use App\Repository\HallRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=HallRepository::class)
 * @ORM\Table(name="`lacker_hall`")
 */
class Hall
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="halls")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Restaurant $restaurant;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getRestaurantId(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurantId(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;
        return $this;
    }
}
