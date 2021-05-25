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
#[ConfiguratorAttribute('app.config.table')]
class Appeal extends BaseObject
{
	public const TARGET_INFO = 'INFO', TARGET_PAY = 'PAY', TARGET_OTHER = 'OTHER';
	/**
	 * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="appeals")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('table', 'getAppealTable', immutable: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private ?Table $appealTable;

	/**
	 * @ORM\ManyToOne(targetEntity=User::class, inversedBy="appeals")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Field('user', 'getUser', immutable: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private ?User $user;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	#[Field('target', 'getTarget', immutable: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	#[Assert\Choice([Appeal::TARGET_INFO,Appeal::TARGET_PAY,Appeal::TARGET_OTHER], groups: ["create", "update"])]
	private string $target;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	#[Field('comment', 'getComment', immutable: true)]
	private string $comment;

	/**
	 * @ORM\Column(type="boolean")
	 */
	#[Field('checked', 'isChecked', setter: 'setChecked', immutable: true)]
	#[Assert\NotNull(groups: ["create", "update"])]
	private bool $checked;

	public function __construct(
		User $user,
		Table $table,
		string $target,
		string $comment = null,
	)
	{
		$this->setUser($user)
			->setAppealTable($table)
			->setComment($comment)
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

	public function setUser(?User $User): self
	{
		$this->user = $User;

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

	public function getComment(): ?string
	{
		return $this->comment;
	}

	public function setComment(string $comment): self
	{
		$this->comment = $comment;

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
