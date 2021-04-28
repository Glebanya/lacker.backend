<?php

namespace App\Configurators\Entity;

use App\Api\ConfiguratorInterface;
use App\Entity\Dish as DishEntity;
use App\Entity\Portion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DishConfig extends BaseConfigurator implements ConfiguratorInterface
{
	public function __construct(protected EntityManagerInterface $manager, protected ValidatorInterface $validator)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return DishEntity::class;
	}

	protected function getMethodsList(): array
	{
		return [
			'remove_portion' => function(DishEntity $object, array $params) {

				if (array_key_exists('portion', $params) && is_string($params['portion']))
				{
					if ($portion = $this->manager->getPartialReference(Portion::class, $params['portion']))
					{
						$object->removePortion($portion);
						$this->manager->flush();
					}
				}

				return true;
			},
			'add_portion' => function(DishEntity $object, array $params) {

				if (array_key_exists('portion', $params) && is_array($params['portion']))
				{
					if(is_array($rawPortion = $params['portion']))
					{
						if (count($errors = $this->validator->validate($portion = new Portion($rawPortion))) === 0)
						{
							$object->addPortion($portion);
							$this->manager->flush();
						}
					}
				}
			},
			'add_portion_batch' => function(DishEntity $object, array $params) {

				if (array_key_exists('portion', $params) && is_array($params['portion']))
				{
					foreach ($params['portion'] as $rawPortion)
					{
						if (is_array($rawPortion))
						{
							if (count($errors = $this->validator->validate($portion = new Portion($rawPortion['dish']))) === 0)
							{
								$object->addPortion($portion);
							}
						}
					}
					$this->manager->flush();
				}
			}

		];
	}
}