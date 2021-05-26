<?php

namespace App\Entity;

use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use App\Repository\AppealRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AppealRepository::class)
 */
#[ConfiguratorAttribute('app.config.appeal')]
class Appeal extends BaseObject
{
	public const TARGET_INFO = 'INFO', TARGET_PAY_BANK = 'PAY_BANK', TARGET_PAY_CASH = 'PAY_CASH',TARGET_OTHER = 'OTHER';
	/**
	 * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="appeals")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('table', 'getAppealTable', immutable: true, default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private ?Table $appealTable;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="appeals")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('user', 'getUser', immutable: true, default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private ?User $user;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field('target', 'getTarget', immutable: true, default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Assert\Choice([Appeal::TARGET_INFO,Appeal::TARGET_PAY_BANK,Appeal::TARGET_PAY_CASH], groups: ["create", "update"])]
	private string $target;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Field('checked', 'isChecked', setter: 'setChecked', immutable: false, default: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private bool $checked;

	public function __construct(
		User $user,
		Table $table,
		string $target,
	)
	{
		$this->setUser($user)
			->setAppealTable($table)
			->setTarget($target)
			->setChecked(false)
			;
	}

	#[Reference('table')]
	public function getAppealTable(): ?Table
	{
		return $this->appealTable;
	}

	public function setAppealTable(?Table $appealTable): self
	{
		$this->appealTable = $appealTable;

		return $this;
	}

	#[Reference('user')]
	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): self
	{
		$this->user = $user;

		return $this;
	}

	public function getTarget(): ?string
	{
		return $this->target;
	}

	public function setTarget(string $target): self
	{
		$this->target = $target;

		return $this;
	}


	public function isChecked(): ?bool
	{
		return $this->checked;
	}

	public function setChecked(bool $checked): self
	{
		$this->checked = $checked;

		return $this;
	}
}
