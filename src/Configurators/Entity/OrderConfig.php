<?php

namespace App\Configurators\Entity;

use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Configurators\Exception\EntityNotFoundException;
use App\Configurators\Exception\ParameterException;
use App\Configurators\Exception\ValidationException;
use App\Entity\Order;
use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Entity\SubOrder;
use App\Repository\PortionRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderConfig extends BaseConfigurator
{
	public function __construct(
		protected EntityManagerInterface $manager,
		protected ValidatorInterface $validator,
		protected ApiService $apiService,
		protected Serializer $serializer
	)
	{
		parent::__construct();
	}

	protected function getEntity(): string
	{
		return Order::class;
	}

	protected function getMethodsList(): array
	{
		return array_merge_recursive(parent::getMethodsList(), [
				'add_suborder' => function (Order $order, array $parameters)
				{
					if (array_key_exists('portions',$parameters) && is_array($portions = $parameters['portions']))
					{
						$subOrder = new SubOrder(
							order: $order,
							comment: array_key_exists('comment',$parameters) && is_string($parameters['comment']) ?
								$parameters['comment'] :
								null,
							portions: array_map(
									function($portion) {
										$object = $this->manager->find(Portion::class, $portion);
										return $object && !$object->isDeleted() ?
												$object :
												throw new EntityNotFoundException((string) $portion)
											;
									},
									$portions
							)
						);
						if (count($errors = $this->validator->validate($subOrder, groups: "update")) === 0)
						{
							$this->manager->flush();
							return $this->serializer->serialize(
								$this->apiService->buildApiEntityObject($order)
							);
						}
						throw new ValidationException($errors);
					}
					throw new ParameterException("wrong params");
				},
				'remove_suborder' => function (Order $order, array $parameters)
				{
					if (array_key_exists('suborder',$parameters) && is_string($subOrder = $parameters['suborder']))
					{
						if ($subOrder = $this->manager->find(SubOrder::class,$subOrder))
						{
							$order->removeSubOrder($subOrder);
							$this->manager->flush();
							return $order;
						}
						throw new EntityNotFoundException($parameters['suborder']);
					}
					throw new ParameterException("wrong params");
				}
			]
		);
	}
}