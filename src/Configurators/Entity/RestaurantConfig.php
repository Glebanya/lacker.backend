<?php

namespace App\Configurators\Entity;

use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Menu;
use App\Entity\Restaurant;
use App\Entity\Restaurant as RestaurantEntity, App\Api\ConfiguratorInterface;
use App\Entity\Staff;
use App\Lang\LangService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestaurantConfig extends BaseConfigurator implements ConfiguratorInterface
{
	protected const ENTITY_CLASS = RestaurantEntity::class;

	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected Security $security,
		protected LangService $langService
	)
	{
		parent::__construct($this->langService);
	}

	protected function getEntity(): string
	{
		return RestaurantEntity::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(
			parent::getMethodsList(),[
			'add_menu' => function(RestaurantEntity $object, array $params)
			{
				if (array_key_exists('menu',$params) && is_array($params['menu']))
				{
					$errors = $this->validator->validate($menu = new Menu($params['menu']), groups: "create");
					if (0 === count($errors))
					{
						$object->addMenu($menu);
						$this->manager->persist($menu);
						$this->manager->flush();
						return $object->getId();
					}
					throw new ValidationException($errors);
				}
				throw new ParameterException("wrong params");
			},
			'add_staff' => function(RestaurantEntity $object, array $params)
			{
				if (array_key_exists('staff',$params) && is_array($rawStaff = $params['staff']))
				{
					$errors = $this->validator->validate($staff = new Staff($rawStaff['staff']), groups: "create");
					if (count($errors) === 0)
					{
						$object->addStaff($staff);
						$this->manager->persist($staff);
						$this->manager->flush();
					}
					throw new ValidationException($errors);
				}
				throw new ParameterException("wrong params");
			},
		]);
	}
}