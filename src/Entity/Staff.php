<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Immutable;
use App\Configurators\Attributes\Reference;
use App\Repository\StaffRepository;
use App\Utils\Environment;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=StaffRepository::class)
 */
#[ConfiguratorAttribute('app.config.staff')]
class Staff extends BaseUser implements UserInterface, EquatableInterface
{
	public const STATUS_WORKING = 'WORKING', STATUS_NOT_WORKING = 'NOT_WORKING' , STATUS_BUSY = 'BUSY';
	public const ROLE_ADMINISTRATOR = 'ROLE_ADMIN', ROLE_MANAGER = 'ROLE_MANAGER' , ROLE_STAFF = 'ROLE_STAFF';

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Assert\NotCompromisedPassword(groups: ["create","update"])]
	#[Assert\NotBlank(groups: ["create","update"])]
	#[Assert\Length(
		min: 6,
		max: 15,
		minMessage: 'Your password must be at least {{ limit }} characters long',
		maxMessage: 'Your password cannot be longer than {{ limit }} characters',
	)]
	private ?string $password;
	/**
	 * @ORM\Column(type="simple_array")
	 * @Assert\All({
	 *     @Assert\NotBlank,
	 *     @Assert\Choice({"ROLE_ADMIN","ROLE_MANAGER","ROLE_STAFF"},groups= {"create", "update"}),
	 * })
	 */
	#[Field(name: "role")]
	#[Assert\Choice([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_STAFF, Staff::ROLE_MANAGER])]
	private string $role;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Assert\Choice([Staff::STATUS_NOT_WORKING, Staff::STATUS_NOT_WORKING, Staff::STATUS_BUSY])]
	private ?string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="staff")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private ?Restaurant $restaurant;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	#[Immutable]
	#[Field(name: 'firebase_token')]
	private ?string $firebaseToken;

	public function __construct($params = [])
	{
		parent::__construct($params);
		if (array_key_exists('password', $params) && is_string($params['password']))
		{
			$this->password = $params['password'];
		}
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
		return [$this->role];
	}

	public function setRoles(string $role): self
	{
		$this->role = $role;
		return $this;
	}

	public function getSalt(): ?string
	{
		return Environment::get('STAFF_SALT');
	}

	#[Reference('restaurant')]
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

	public function setStatus(string $status) : self
	{
		$this->status = $status;
		return $this;
	}
	/**
	 * @ORM\PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
		$this->getRestaurant()?->onUpdate();
	}

	/**
	 * @ORM\PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->status = static::STATUS_NOT_WORKING;
		$this->getRestaurant()?->onUpdate();
	}
}
