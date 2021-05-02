<?php

namespace App\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DishRepository;
use App\Types\Image;
use App\Types\Lang;
use App\Api\Attributes\ConfiguratorAttribute;
use App\Configurators\Attributes\Collection as CollectionAttribute;
use App\Configurators\Attributes\Field;
use App\Configurators\Attributes\Reference;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * @ORM\Entity(repositoryClass=DishRepository::class)
 * @HasLifecycleCallbacks
 */
#[ConfiguratorAttribute('app.config.dish')]
class Dish extends BaseObject
{
	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'description')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $description;

	/**
	 * @ORM\OneToMany(
	 *     targetEntity=Portion::class,
	 *     mappedBy="dish",
	 *     orphanRemoval=true
	 *)
	 * @Assert\All({
	 *      @Assert\Type("\App\Entity\Portion")
	 * })
	 */
	#[Reference(name: 'portions')]
	#[CollectionAttribute]
	#[Assert\Count(
		min: 1,
		max: 10,
		minMessage: "portions count must be more than {{ limit }}",
		maxMessage: "portions count must be less than {{ limit }}"
	)]
	#[Assert\Valid]
	private Collection $portions;

	/**
	 * @ORM\Column(type="lang_phrase")
	 */
	#[Field(name: 'name')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Lang $name;

	/**
	 * @ORM\Column(type="image", nullable=true)
	 */
	#[Field(name: 'name')]
	#[Assert\Valid(groups: ["create", "update"])]
	private Image $image;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	#[Field(name: 'type')]
	#[Assert\Choice(['DRINKS', 'DISH', 'ALCOHOL', null], groups: ["create", "update"])]
	private ?string $type;

	/**
	 * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="dishes")
	 * @ORM\JoinColumn(nullable=false)
	 */
	#[Reference(name: "menu")]
	private ?Menu $menu;

	public function __construct($params = [])
	{
		$this->portions = new ArrayCollection();
		if (array_key_exists('description', $params) && is_array($params['description']))
		{
			$this->description = new Lang($params['description']);
		}
		if (array_key_exists('name', $params) && is_array($params['name']))
		{
			$this->name = new Lang($params['name']);
		}
		if (array_key_exists('type', $params) && is_string($params['type']))
		{
			$this->type = $params['type'];
		}
		if (array_key_exists('image', $params) && is_string($params['image']))
		{
			$this->type = $params['image'];
		}
		if (array_key_exists('portions', $params) && is_array($params['portions']))
		{
			foreach ($params['portions'] as $portion)
			{
				if (is_array($portion))
				{
					$this->portions->add(new Portion($portion));
				}
			}
		}
	}

	public function getDescription(): ?Lang
	{
		return $this->description;
	}

	public function setDescription(array $description): self
	{
		$this->description = new Lang($description);

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getPortions(): Collection
	{
		return $this->portions;
	}

	public function addPortion(Portion $portion): self
	{
		if (!$this->portions->contains($portion))
		{
			$this->portions[] = $portion;
			$portion->setDish($this);
		}

		return $this;
	}

	public function removePortion(Portion $portion): self
	{
		if ($this->portions->removeElement($portion))
		{
			if ($portion->getDish() === $this)
			{
				$portion->setDish(null);
			}
		}

		return $this;
	}

	public function getName(): ?Lang
	{
		return $this->name;
	}

	public function setName(array $name): self
	{
		$this->name = new Lang($name);

		return $this;
	}

	public function getImage(): Image
	{
		return $this->image;
	}

	public function setImage($image): self
	{
		$this->image = $image;

		return $this;
	}

	public function getType(): ?string
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		$this->type = $type;

		return $this;
	}

	//	#[Assert\Callback]
	//	public function validate(ExecutionContextInterface $context)
	//	{
	//		foreach ($this->portions as $portion)
	//		{
	//			$context->addViolation($context->getValidator()->validate($portion));
	//		}
	//	}

	public function getMenu(): ?Menu
	{
		return $this->menu;
	}

	public function setMenu(?Menu $menu): self
	{
		$this->menu = $menu;

		return $this;
	}

	/**
	 * @PreUpdate
	 *
	 * @param PreUpdateEventArgs|null $eventArgs
	 */
	public function onUpdate(PreUpdateEventArgs $eventArgs = null)
	{
		parent::onUpdate($eventArgs);
		$this->getMenu()?->onUpdate($eventArgs);
		$this->getMenu()?->getRestaurant()?->onUpdate($eventArgs);
	}

	/**
	 * @PrePersist
	 *
	 * @param LifecycleEventArgs|null $eventArgs
	 */
	public function onAdd(LifecycleEventArgs $eventArgs = null)
	{
		parent::onAdd($eventArgs);
		$this->getMenu()?->onUpdate();
		$this->getMenu()?->getRestaurant()?->onUpdate();
	}
}
