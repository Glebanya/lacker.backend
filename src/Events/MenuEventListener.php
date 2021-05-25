<?php

namespace App\Events;

use App\Entity\Menu;
use Doctrine\ORM\Event\LifecycleEventArgs;

class MenuEventListener
{
	public function prePersist(Menu $menu, LifecycleEventArgs $eventArgs) : void
	{
		$menu->onAdd();
	}

	public function preUpdate(Menu $menu, LifecycleEventArgs $event) : void
	{
		$menu->onUpdate();
	}
}