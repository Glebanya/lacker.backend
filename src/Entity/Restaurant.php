<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;
/**
 * @ORM\Entity(repositoryClass=RestaurantRepository::class)
 * @ORM\Table(name="`lacker_restaurant`")
 */
class Restaurant
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
    private ?string $timezone;

    /**
     * @ORM\OneToMany(targetEntity=Hall::class, mappedBy="restaurant_id", orphanRemoval=true)
     */
    private ArrayCollection $halls;

    /**
     * @ORM\OneToMany(targetEntity=RestaurantResourceText::class, mappedBy="restaurant", orphanRemoval=true)
     */
    private ArrayCollection $resourceText;

    /**
     * @ORM\OneToMany(targetEntity=RestaurantResourceSettings::class, mappedBy="restaurant", orphanRemoval=true)
     */
    private ArrayCollection $settings;

    public function __construct()
    {
        $this->halls = new ArrayCollection();
        $this->resourceText = new ArrayCollection();
        $this->settings = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return Collection|Hall[]
     */
    public function getHalls(): Collection
    {
        return $this->halls;
    }

    public function addHall(Hall $hall): self
    {
        if (!$this->halls->contains($hall)) {
            $this->halls[] = $hall;
            $hall->setRestaurantId($this);
        }

        return $this;
    }

    public function removeHall(Hall $hall): self
    {
        if ($this->halls->removeElement($hall)) {
            // set the owning side to null (unless already changed)
            if ($hall->getRestaurantId() === $this) {
                $hall->setRestaurantId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RestaurantResourceText[]
     */
    public function getResourceText(): Collection
    {
        return $this->resourceText;
    }

    public function addResourceText(RestaurantResourceText $resourceText): self
    {
        if (!$this->resourceText->contains($resourceText)) {
            $this->resourceText[] = $resourceText;
            $resourceText->setRestaurant($this);
        }

        return $this;
    }

    public function removeResourceText(RestaurantResourceText $resourceText): self
    {
        if ($this->resourceText->removeElement($resourceText)) {
            // set the owning side to null (unless already changed)
            if ($resourceText->getRestaurant() === $this) {
                $resourceText->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RestaurantResourceSettings[]
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    public function addSetting(RestaurantResourceSettings $setting): self
    {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setRestaurant($this);
        }

        return $this;
    }

    public function removeSetting(RestaurantResourceSettings $setting): self
    {
        if ($this->settings->removeElement($setting)) {
            // set the owning side to null (unless already changed)
            if ($setting->getRestaurant() === $this) {
                $setting->setRestaurant(null);
            }
        }

        return $this;
    }
}
