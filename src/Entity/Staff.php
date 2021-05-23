<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\StaffRepository;
use App\Types\Image;
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
#[Field('restaurant', 'getRestaurant', immutable: true)]
class Staff extends BaseUser implements UserInterface, EquatableInterface
{
	public const STATUS_WORKING = 'WORKING', STATUS_NOT_WORKING = 'NOT_WORKING' , STATUS_BUSY = 'BUSY';
	public const ROLE_ADMINISTRATOR = 'ROLE_ADMIN', ROLE_MANAGER = 'ROLE_MANAGER' , ROLE_STAFF = 'ROLE_STAFF';

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Assert\NotCompromisedPassword(groups: ["create"])]
	#[Assert\NotBlank(groups: ["create"])]
	#[Assert\Length(
		min: 6,
		max: 15,
		minMessage: 'Your password must be at least {{ limit }} characters long',
		maxMessage: 'Your password cannot be longer than {{ limit }} characters',
		groups: ["create"]
	)]
	protected ?string $password;
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: "role", getter: 'getRole',setter: 'setRoles', default: true)]
	#[Assert\Choice(
		[Staff::ROLE_ADMINISTRATOR, Staff::ROLE_STAFF, Staff::ROLE_MANAGER],
		groups: ["create","update"]
	)]
	protected string $roles;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: "status", getter: 'getStatus', setter: 'setStatus', default: true)]
	#[Assert\Choice(
		[Staff::STATUS_WORKING, Staff::STATUS_NOT_WORKING, Staff::STATUS_BUSY],
		groups: ["create","update"]
	)]
	private ?string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="staff")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private ?Restaurant $restaurant;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	#[Field(name: 'firebase_token', getter: 'getFirebaseToken', setter: 'setFirebaseToken',default: true)]
	private ?string $firebaseToken;


	public function __construct($params = [])
	{
		parent::__construct($params);
		if (array_key_exists('password', $params) && is_string($params['password']))
		{
			$this->password = $params['password'];
		}
		if (array_key_exists('role', $params) && is_string($params['role']))
		{
			$this->roles = $params['role'];
		}
		if (array_key_exists('firebase_token', $params) && is_string($params['firebase_token']))
		{
			$this->firebaseToken = $params['firebase_token'];
		}
		if (array_key_exists('picture',$params) && is_string($params['picture']))
		{
			$this->avatar = new Image($params['picture']);
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

	public function getRole() : ?string
	{
		return $this->roles;
	}

	public function getRoles(): ?array
	{
		return [$this->roles];
	}

	public function setRoles(string $role): self
	{
		$this->roles = $role;
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

	public function getStatus() : ?string
	{
		return $this->status;
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
