<?php

namespace App\Configurators\Entity;

use App\Api\ConfiguratorInterface;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Dish as DishEntity;
use App\Entity\Portion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DishConfig extends BaseConfigurator implements ConfiguratorInterface
{
	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
	)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return DishEntity::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(
			parent::getMethodsList(),
			[
				'add_portion' => function(DishEntity $object, array $params)
				{
					if (array_key_exists('portion', $params) && is_array($rawPortion = $params['portion']))
					{
						$object->addPortion($portion = new Portion($rawPortion));
						if (count($errors = $this->validator->validate($portion, groups: "create")) === 0)
						{
							$this->manager->persist($portion);
							$this->manager->flush();
							return $object->getId();
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				}
		]);
	}
}