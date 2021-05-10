<?php

namespace App\Configurators\Entity;

use App\Configurators\Exception\EntityNotFoundException;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Order;
use App\Entity\Restaurant;
use App\Lang\LangService;
use App\Repository\PortionRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderConfig extends BaseConfigurator
{
	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected PortionRepository $portionRepository,
		protected LangService $langService
	)
	{
		parent::__construct($this->langService);
	}

	protected function getEntity(): string
	{
		return Order::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(
			parent::getMethodsList(),
			[
				'add_portion' => function (Order $order, array $parameters)
				{
					if (array_key_exists('portions',$parameters) && is_array($portions = $parameters['portions']))
					{
						$this->portionRepository->matching(
							Criteria::create()->where(
								Criteria::expr()->in('id',$portions)
							)->andWhere(
								Criteria::expr()->eq('deleted',false)
							)
						)->forAll(
							function($dish) use ($order) {
								$order->addPortion($dish);
							});

						$errors = $this->validator->validate($order, groups: "update");
						if (count($errors) === 0)
						{
							$this->manager->flush();
							return $order->getId();
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				},
				'remove_portion' => function(Order $order, array $parameters)
				{
					if (array_key_exists('portions',$parameters) && is_array($portions = $parameters['portions']))
					{
						$this->portionRepository->matching(
							Criteria::create()->where(
								Criteria::expr()->in('id',$portions)
							)->andWhere(
								Criteria::expr()->eq('deleted',false)
							)
						)->forAll(
							function($dish) use ($order) {
								$order->removePortion($dish);
							});

						$errors = $this->validator->validate($order, groups: "update");
						if (count($errors) === 0)
						{
							$this->manager->flush();
							return $order->getId();
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				}
			]
		);
	}
}