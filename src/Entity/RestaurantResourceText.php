<?php

namespace App\Entity;

use App\Repository\RestaurantResourceTextRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;
/**
 * @ORM\Entity(repositoryClass=RestaurantResourceTextRepository::class)
 */
class RestaurantResourceText
{

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="string", length=127)
     */
    private ?string $lang_type;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $value;

    /**
     * @ORM\Column(type="string", length=127)
     */
    private ?string $type;

    /**
     * @ORM\ManyToOne(targetEntity=restaurant::class, inversedBy="resourceText")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurant;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLangType(): ?string
    {
        return $this->lang_type;
    }

    public function setLangType(string $lang_type): self
    {
        $this->lang_type = $lang_type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRestaurant(): ?restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}
