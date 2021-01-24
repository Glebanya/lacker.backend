<?php

namespace App\Entity;

use App\Repository\HallRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id",nullable=false)
     */
    private ?Restaurant $restaurant;

    /**
     * @ORM\OneToMany(targetEntity=Table::class, mappedBy="hall", orphanRemoval=true)
     */
    private ArrayCollection $tables;

    public function __construct()
    {
        $this->tables = new ArrayCollection();
    }

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

    /**
     * @return Collection|Table[]
     */
    public function getTables(): Collection
    {
        return $this->tables;
    }

    public function addTable(Table $table): self
    {
        if (!$this->tables->contains($table)) {
            $this->tables[] = $table;
            $table->setHall($this);
        }

        return $this;
    }

    public function removeTable(Table $table): self
    {
        if ($this->tables->removeElement($table)) {
            // set the owning side to null (unless already changed)
            if ($table->getHall() === $this) {
                $table->setHall(null);
            }
        }

        return $this;
    }
}
