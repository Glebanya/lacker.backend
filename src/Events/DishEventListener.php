<?php

namespace App\Events;

use App\Entity\Dish;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DishEventListener
{
	public function prePersist(Dish $dish, LifecycleEventArgs $eventArgs) : void
	{
		$dish->onAdd();
	}

	public function preUpdate(Dish $dish, LifecycleEventArgs $event) : void
	{
		$dish->onUpdate();
	}
}