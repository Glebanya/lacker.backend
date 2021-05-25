<?php

namespace App\Events;

use App\Entity\Portion;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PortionListener
{
	public function prePersist(Portion $portion, LifecycleEventArgs $eventArgs) : void
	{
		$portion->onAdd();
	}

	public function preUpdate(Portion $portion, LifecycleEventArgs $event) : void
	{
		$portion->onUpdate();
	}
}