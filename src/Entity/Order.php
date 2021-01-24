<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`lacker_order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $create_date;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private ?string $status;

    /**
     * @ORM\ManyToMany(targetEntity=DishPortion::class)
     */
    private ArrayCollection $dishes;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private ?string $currency_type;

    public function __construct()
    {
        $this->dishes = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->create_date;
    }

    public function setCreateDate(\DateTimeInterface $create_date): self
    {
        $this->create_date = $create_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|DishPortion[]
     */
    public function getDishes(): Collection
    {
        return $this->dishes;
    }

    public function addDish(DishPortion $dish): self
    {
        if (!$this->dishes->contains($dish)) {
            $this->dishes[] = $dish;
        }

        return $this;
    }

    public function removeDish(DishPortion $dish): self
    {
        $this->dishes->removeElement($dish);

        return $this;
    }

    public function getCurrencyType(): ?string
    {
        return $this->currency_type;
    }

    public function setCurrencyType(string $currency_type): self
    {
        $this->currency_type = $currency_type;

        return $this;
    }
}
