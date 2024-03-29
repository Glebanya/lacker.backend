<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\TableReserveRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TableReserveRepository::class)
 */
#[ConfiguratorAttribute('app.config.menu')]
#[Field('table', 'getReservedTable', immutable: true)]
#[Field('user', 'getUser', immutable: true)]
class TableReserve extends BaseObject
{

	public const STATUS_NEW = "NEW", STATUS_CLOSED = "CLOSED";
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field(name: 'status',getter: 'getStatus',setter: 'setStatus', default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Assert\Choice([TableReserve::STATUS_NEW, TableReserve::STATUS_CLOSED], groups: ["create", "update"])]
	protected ?string $status;

	/**
	 * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="tableReserves")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create", "update"])]
	protected ?Table $__table;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tableReserves")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Assert\NotNull(groups: ["create", "update"])]
	protected ?User $user;

	public function __construct(User $user, Table $table)
	{
		$this->setUser($user)->setReservedTable($table);
	}

	public function getStatus(): ?string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;

		return $this;
	}

	#[Reference(name: 'table')]
	public function getReservedTable(): ?Table
	{
		return $this->__table;
	}

	public function setReservedTable(?Table $__table): self
	{
		$this->__table = $__table;
		return $this;
	}

	#[Reference(name: 'user')]
	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
	}
}
