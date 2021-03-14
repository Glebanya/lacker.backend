<?php

namespace App\Entity;

use App\Repository\BusinessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;

/**
 * @ORM\Entity(repositoryClass=BusinessRepository::class)
 */
class Business
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private ?Uuid $id;

    /**
     * @ORM\OneToMany(targetEntity=Restaurant::class, mappedBy="business", orphanRemoval=true)
     */
    private Collection $restaurants;

    /**
     * @ORM\OneToMany(targetEntity=Access::class, mappedBy="business", orphanRemoval=true)
     */
    private Collection $accesses;

    /**
     * @ORM\OneToMany(targetEntity=Stuff::class, mappedBy="business", orphanRemoval=true)
     */
    private $stuff;

    public function __construct()
    {
        $this->accesses = new ArrayCollection();
        $this->stuff = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return Collection|Restaurant[]
     */
    public function getRestaurants(): Collection
    {
        return $this->restaurants;
    }

    public function addRestaurant(Restaurant $restaurant): self
    {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants[] = $restaurant;
            $restaurant->setBusiness($this);
        }

        return $this;
    }

    public function removeRestaurant(Restaurant $restaurant): self
    {
        if ($this->restaurants->removeElement($restaurant)) {
            // set the owning side to null (unless already changed)
            if ($restaurant->getBusiness() === $this) {
                $restaurant->setBusiness(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAccesses(): Collection
    {
        return $this->accesses;
    }

    public function addAccess(Access $access): self
    {
        if (!$this->accesses->contains($access)) {
            $this->accesses[] = $access;
            $access->setBusiness($this);
        }

        return $this;
    }

    public function removeAccess(Access $access): self
    {
        if ($this->accesses->removeElement($access)) {
            // set the owning side to null (unless already changed)
            if ($access->getBusiness() === $this) {
                $access->setBusiness(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stuff[]
     */
    public function getStuff(): Collection
    {
        return $this->stuff;
    }

    public function addStuff(Stuff $stuff): self
    {
        if (!$this->stuff->contains($stuff)) {
            $this->stuff[] = $stuff;
            $stuff->setBusiness($this);
        }

        return $this;
    }

    public function removeStuff(Stuff $stuff): self
    {
        if ($this->stuff->removeElement($stuff)) {
            // set the owning side to null (unless already changed)
            if ($stuff->getBusiness() === $this) {
                $stuff->setBusiness(null);
            }
        }

        return $this;
    }
}
