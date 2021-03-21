<?php

namespace App\Entity;

use App\Repository\PortionRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @ORM\Entity(repositoryClass=PortionRepository::class)
 */
class Portion implements IExportable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Dish::class, inversedBy="portions")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Dish $dish;

    /**
     * @ORM\Column(type="json")
     */
    private array $price = [];

    /**
     * @ORM\Column(type="json")
     */
    private array $size = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDish(): ?Dish
    {
        return $this->dish;
    }

    public function setDish(?Dish $dish): self
    {
        $this->dish = $dish;
        return $this;
    }

    public function getPrice(): ?array
    {
        return $this->price;
    }

    public function setPrice(array $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getSize(): ?array
    {
        return $this->size;
    }

    public function setSize(array $size): self
    {
        $this->size = $size;
        return $this;
    }

    #[ArrayShape(['id' => "int|null", 'size' => "mixed|string", 'price' => "mixed|string"])]
    public function export(string $locale): array
    {
        return [
            'id' => $this->getId(),
            'size' => array_key_exists($locale, $this->size)? $this->size[$locale] : '',
            'price' => array_key_exists($locale, $this->price)? $this->price[$locale] : '',
        ];
    }
}
