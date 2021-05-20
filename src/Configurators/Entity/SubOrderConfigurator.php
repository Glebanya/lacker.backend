<?php

namespace App\Configurators\Entity;

use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Order;
use App\Entity\Portion;
use App\Entity\SubOrder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubOrderConfigurator extends BaseConfigurator
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
		return SubOrder::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), [
			'remove_portions' => function(SubOrder $subOrder, array $parameters)
			{
				if (array_key_exists('portions',$parameters) and is_array($portions = $parameters['portions']))
				{
					$this->manager->getRepository(Portion::class)->matching(
						Criteria::create()->andWhere(
							Criteria::expr()->in(
								'id',
								array_map(
									function($item) : Uuid
									{
										return is_string($item)?
											new Uuid($item) :
											throw new \Exception("wrong portion id")
											;
									},
									$portions
								)
							)
						)->andWhere(
							Criteria::expr()->eq('deleted',false)
						)
					)->forAll(function(Portion $item) use ($subOrder) {
						$subOrder
							->removePortion($item)
							->setChecked(false);
					});

					if (count($errors = $this->validator->validate($subOrder, groups: "update")) === 0)
					{
						$this->manager->flush();
						return $subOrder;
					}
					throw new ValidationException($errors);
				}
				throw new ParameterException("wrong params");
			}
		]);
	}
}