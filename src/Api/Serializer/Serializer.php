<?php

namespace App\Api\Serializer;

use App\Api\ApiEntity;
use App\Api\ApiEntityCollection;
use App\Api\ApiService;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;

class Serializer
{
	/**
	 * Serializer constructor.
	 *
	 * @param ApiService $apiService
	 * @param RequestStack $requestStack
	 * @param Normalizer $normalizer
	 */
	public function __construct(
		private ApiService $apiService,
		private RequestStack $requestStack,
		private Normalizer $normalizer
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
	public function serializeCollection(ApiEntityCollection $collection, array|null $requestedFields = null): array
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
	public function serializeObject(ApiEntity $entity, array|null $requestedFields = null): array
	{

		return array_reduce(
			$requestedFields ?? $entity->getDefaultFieldsNames(),
			function($serialized, $property) use ($entity) {
				$serialized[$property] = $this->normalizer->normalize($entity->getProperty($property));

				return $serialized;
			},
			[]
		);
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