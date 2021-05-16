<?php

namespace App\Api\Serializer;

use App\Api\Access;
use App\Api\ApiEntity;
use App\Api\ApiEntityCollection;
use App\Api\ApiService;
use App\Lang\LangService;
use App\Types\Lang;
use DateTimeInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

class Serializer
{
	/**
	 * Serializer constructor.
	 *
	 * @param ApiService $apiService
	 * @param LangService $langService
	 * @param RequestStack $requestStack
	 * @param Access $access
	 */
	public function __construct(
		private ApiService $apiService,
		private LangService $langService,
		private RequestStack $requestStack,
		private Access $access
	)
	{

	}

	/**
	 * @return string[]|null
	 */
	private function getRequestedFields(): array|null
	{
		if ($raw = $this->requestStack->getCurrentRequest()->query->get('fields', default: null))
		{

			return array_filter(array_map('trim', explode(',', $raw)),
				function(string $fields): bool {
					return !empty($fields);
				});
		}

		return null;
	}
	/**
	 * @param ApiEntityCollection $collection
	 * @param array|null $requestedFields
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function serializeCollection(ApiEntityCollection $collection, array|null $requestedFields): array
	{

		return array_reduce(
			iterator_to_array($collection),
			function($serialized, ApiEntity $entity) use ($requestedFields) {
				$serialized[] = $this->serializeObject($entity, $requestedFields);

				return $serialized;
			},
			[]
		);
	}

	/**
	 * @param ApiEntity $entity
	 * @param array|null $requestedFields
	 *
	 * @return array
	 * @throws Exception
	 */
	public function serializeObject(ApiEntity $entity, array|null $requestedFields): array
	{

		return array_reduce(
			$requestedFields ?? $entity->getDefaultFieldsNames(),
			function($serialized, $property) use ($entity) {
				$serialized[$property] = $this->serializeValue($entity->getProperty($property));

				return $serialized;
			},
			[]
		);
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function serializeValue($value): mixed
	{
		if ($value instanceof DateTimeInterface)
		{
			return $value->getTimestamp();
		}
		elseif ($value instanceof Lang)
		{
			return $this->langService->formatLangObject($value);
		}
		elseif ($value instanceof ApiEntityCollection)
		{
			$this->access->denyAccessUnlessGranted('view',$value);
			return $this->serializeCollection($value,null);
		}
		elseif ($value instanceof ApiEntity)
		{
			$this->access->denyAccessUnlessGranted('view',$value);
			return $this->serializeObject($value,null);
		}

		return $value;
	}

	/**
	 * @param ApiEntity|ApiEntityCollection $object
	 *
	 * @return array
	 * @throws Exception
	 */
	public function serialize(ApiEntity|ApiEntityCollection $object): array
	{
		if ($object instanceof ApiEntity)
		{
			return $this->serializeObject($object,$this->getRequestedFields() ?? $object->getFullFieldsNames());
		}
		else
		{
			return $this->serializeCollection($object,$this->getRequestedFields());
		}
	}
}