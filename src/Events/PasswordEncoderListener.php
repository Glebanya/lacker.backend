<?php

namespace App\Events;

use App\Entity\BaseUser;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderListener implements EventSubscriber
{
	public function __construct(private UserPasswordEncoderInterface $encoder)
	{
	}

	public function getSubscribedEvents(): array
	{
		return [
			Events::preUpdate,
			Events::prePersist
		];
	}

	public function prePersist(LifecycleEventArgs $eventArgs) : void
	{
		if (($entity = $eventArgs->getObject()) && $entity instanceof BaseUser)
		{
			$entity->setPassword($this->encoder->encodePassword($entity,$entity->getPassword()));
		}
	}

	public function preUpdate(PreUpdateEventArgs $eventArgs): void
	{
		if (
			$eventArgs->hasChangedField('password') &&
			($entity = $eventArgs->getObject()) &&
			$entity instanceof BaseUser
		)
		{
			$entity->setPassword($this->encoder->encodePassword($entity,$entity->getPassword()));
		}

	}
}