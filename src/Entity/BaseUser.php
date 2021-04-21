<?php


namespace App\Entity;


use App\Configurators\Attributes\Field;
use JetBrains\PhpStorm\Pure;
use App\Repository\BaseUserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BaseObjectRepository;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="entity_name", type="string")
 * @ORM\Entity(repositoryClass=BaseUserRepository::class)
 * @ORM\DiscriminatorMap({
 *     "staff" = "App\Entity\Staff",
 *     "user" = "App\Entity\User",
 * })
 */
abstract class BaseUser extends BaseObject implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Field(name: 'name')]
    protected ?string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    #[Field(name: 'email')]
    protected ?string $email;

    public function getUsername(): ?string
    {
        return $this->name;
    }

    public function setUsername(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (is_a($user,static::class,false))
        {
            return $this->getId()->compare($user->getId());
        }
        return false;
    }

    public function eraseCredentials()
    {

    }

}