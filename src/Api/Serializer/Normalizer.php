<?php

namespace App\Api\Serializer;

use App\Api\Access;
use App\Api\ApiEntity;
use App\Api\ApiEntityCollection;
use App\Api\Normalizer\DatetimeNormalizer;
use App\Api\Normalizer\LangNormalizer;
use App\Api\Normalizer\NormalizerInterface;
use App\Api\Normalizer\UuidNormalizer;
use App\Types\Lang;
use Psr\Container\ContainerInterface;
use Symfony\Component\Uid\AbstractUid;

class Normalizer implements NormalizerInterface
{
	public function __construct(private ContainerInterface $container, private Access $access)
	{
	}

	public function normalize($value) : mixed
	{
		if ($value instanceof AbstractUid)
		{
			return $this->container->get(UuidNormalizer::class)->normalize($value);
		}
		if ($value instanceof Lang)
		{
			return $this->container->get(LangNormalizer::class)->normalize($value);
		}
		if ($value instanceof \DateTimeInterface)
		{
			return $this->container->get(DatetimeNormalizer::class)->normalize($value);
		}
		if ($value instanceof ApiEntity)
		{
			$this->access->denyAccessUnlessGranted('view',$value);
			return $this->container->get(Serializer::class)->serializeObject($value);
		}
		if ($value instanceof ApiEntityCollection)
		{
			$this->access->denyAccessUnlessGranted('view',$value);
			return $this->container->get(Serializer::class)->serializeCollection($value);
		}
		return $value;
	}

}