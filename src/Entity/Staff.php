<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Immutable;
use App\Configurators\Attributes\Reference;
use App\Repository\UserRepository;
use App\Utils\Environment;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ConfiguratorAttribute('app.config.staff')]
class Staff extends BaseUser
{
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $password;
	/**
	 * @ORM\Column(type="simple_array")
	 * @Assert\All({
	 *     @Assert\NotBlank,
	 *     @Assert\Choice({"ROLE_ADMIN","ROLE_MANAGER","ROLE_STAFF"}),
	 * })
	 */
	#[Field(name: "roles")]
	private array $roles = [];
	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="staff")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference('restaurant')]
	private ?Restaurant $restaurant;
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	#[Field(name: 'firebase_token')]
	#[Immutable]
	private ?string $firebaseToken;

	public function __construct($params = [])
	{
		parent::__construct($params);
		if (array_key_exists('roles', $params) && is_array($params['roles']))
		{
			$this->roles = $params['roles'];
		}
		if (array_key_exists('firebase_token', $params) && is_string($params['firebase_token']))
		{
			$this->firebaseToken = $params['firebase_token'];
		}
	}

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

	public function getSalt(): ?string
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

	public function getFirebaseToken(): ?string
	{
		return $this->firebaseToken;
	}

	public function setFirebaseToken(?string $firebaseToken): self
	{
		$this->firebaseToken = $firebaseToken;

		return $this;
	}
}
