<?php

namespace App\Events;

use App\Entity\SubOrder;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Event\LifecycleEventArgs;

class SuborderListener
{
	public function preUpdate(SubOrder $subOrder, LifecycleEventArgs $event) : void
	{
		$subOrder->getBaseOrder()->setChecked(
			$subOrder->getBaseOrder()
				->getSubOrders()
				->matching(
					Criteria::create()->andWhere(
						Criteria::expr()->eq('checked',false)
					)
				)->isEmpty()
		);
		$event->getEntityManager()->flush();
	}
}