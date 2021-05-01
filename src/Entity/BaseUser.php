<?php

namespace App\Entity;

use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Immutable;
use JetBrains\PhpStorm\Pure;
use App\Repository\BaseUserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
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

	public function __construct($params = [])
	{
		if (array_key_exists('name', $params) && is_string($params['name']))
		{
			$this->name = $params['name'];
		}
		if (array_key_exists('email', $params) && is_string($params['email']))
		{
			$this->email = $params['email'];
		}
	}

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'name')]
	#[Assert\NotBlank(message: 'The email {{ value }} is not a valid name.')]
	#[Assert\Length(
		min: 1,
		max: 255,
		minMessage: 'Name must be at least {{ limit }} characters long',
		maxMessage: 'Name cannot be longer than {{ limit }} characters',
	)]
	protected ?string $name;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	#[Field(name: 'email')]
	#[Immutable]
	#[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
	#[Assert\Unique]
	#[Assert\Length(
		min:1 ,
		max: 255,
		minMessage: 'Email must be at least {{ limit }} characters long',
		maxMessage: 'Email cannot be longer than {{ limit }} characters',
	)]
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
		if (is_a($user, static::class, false))
		{
			return $this->getId()->compare($user->getId()) === 0;
		}

		return false;
	}

	public function eraseCredentials()
	{

	}

}