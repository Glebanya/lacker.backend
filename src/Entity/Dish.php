<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 */
class Dish implements IExportable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="json")
     */
    private array $description = [];

    /**
     * @ORM\OneToMany(targetEntity=Portion::class, mappedBy="dish", orphanRemoval=true)
     */
    private Collection $portions;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="dishes")
     */
    private ?Menu $menu;

    /**
     * @ORM\Column(type="json")
     */
    private array $name = [];

    public function __construct()
    {
        $this->portions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(array $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPortions(): Collection
    {
        return $this->portions;
    }

    public function addPortion(Portion $portion): self
    {
        if (!$this->portions->contains($portion)) {
            $this->portions[] = $portion;
            $portion->setDish($this);
        }

        return $this;
    }

    public function removePortion(Portion $portion): self
    {
        if ($this->portions->removeElement($portion)) {
            if ($portion->getDish() === $this) {
                $portion->setDish(null);
            }
        }

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getName(): ?array
    {
        return $this->name;
    }

    public function setName(array $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function export(string $locale) : array
    {
        return [
            'id' => $this->getId(),
            'description' => array_key_exists($locale, $this->description)? $this->description[$locale] ?? '': '',
            'name' => array_key_exists($locale, $this->name)? $this->name[$locale] ?? '' : '',
            'portions' => $this->getPortions()->map(function ($portion) use ($locale) {
                        if ($portion instanceof Portion)
                        {
                            return $portion->export($locale);
                        }
                        return null;
                    })->filter(function ($serialized) {
                        return isset($serialized) && is_array($serialized);
                    })->toArray(),
        ];
    }
}
