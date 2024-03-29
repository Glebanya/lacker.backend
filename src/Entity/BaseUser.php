<?php

namespace App\Entity;

use App\Configurators\Attributes\Field;
use App\Types\Image;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BaseUserRepository;

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
	#[Field(name: 'name', getter: 'getUsername',setter: 'setUsername',default: true)]
	#[Assert\NotBlank(message: 'The name {{ value }} is not a valid name.', groups: ["create", "update"])]
	#[Assert\Length(
		min: 1,
		max: 255,
		minMessage: 'Name must be at least {{ limit }} characters long',
		maxMessage: 'Name cannot be longer than {{ limit }} characters',
		groups: ["create", "update"]
	)]
	protected ?string $name;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'family_name', getter: 'getFamilyName',setter: 'setFamilyName', default: true)]
	#[Assert\NotBlank(message: 'The family name {{ value }} is not a valid name.', groups: ["create", "update"])]
	#[Assert\Length(
		min: 1,
		max: 255,
		minMessage: 'Name must be at least {{ limit }} characters long',
		maxMessage: 'Name cannot be longer than {{ limit }} characters',
		groups: ["create", "update"]
	)]
	protected ?string $familyName;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	#[Field(name: 'email', getter: 'getEmail',immutable: true, default: true)]
	#[Assert\Email(message: 'The email {{ value }} is not a valid email.', groups: ["create"])]
	#[Assert\NotBlank(message: 'The family name {{ value }} is not a valid name.', groups: ["create", "update"])]
	#[Assert\Length(
		min: 1,
		max: 255,
		minMessage: 'Email must be at least {{ limit }} characters long',
		maxMessage: 'Email cannot be longer than {{ limit }} characters',
		groups: ["create"]
	)]
	protected ?string $email;

	/**
	 * @ORM\Column(type="image", nullable=true)
	 */
	#[Field(name: 'avatar', getter: 'getAvatar', setter: 'setAvatar', default: true)]
	#[Assert\Valid]
	protected ?Image $avatar = null;

	public function __construct($params = [])
	{
		if (array_key_exists('name', $params) && is_string($params['name']))
		{
			$this->name = $params['name'];
		}
		if (array_key_exists('family_name', $params) && is_string($params['family_name']))
		{
			$this->familyName = $params['family_name'];
		}
		if (array_key_exists('email', $params) && is_string($params['email']))
		{
			$this->email = $params['email'];
		}
		if (array_key_exists('avatar', $params) && is_string($params['avatar']))
		{
			$this->avatar = new Image($params['avatar']);
		}
	}

	public function getUsername(): ?string
	{
		return $this->name;
	}

	public function setUsername(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getFamilyName(): ?string
	{
		return $this->familyName;
	}

	public function setFamilyName(string $familyName): self
	{
		$this->familyName = $familyName;
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
			return $this->getId()->equals($user->getId());
		}

		return false;
	}

	public function eraseCredentials()
	{

	}

	public function getAvatar(): ?Image
	{
		return $this->avatar;
	}

	public function setAvatar(?Image $avatar): self
	{
		$this->avatar = $avatar;

		return $this;
	}
}