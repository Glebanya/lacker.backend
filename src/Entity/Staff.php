<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Utils\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class Staff extends BaseUser
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="simple_array")
     */
    private array $roles = [];

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="staff")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Restaurant $restaurant;


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    #[Pure] public function getSalt(): ?string
    {
        return Environment::get('STAFF_SALT');
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}
