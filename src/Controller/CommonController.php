<?php

namespace App\Controller;

use App\Api\ApiEntity;
use App\Api\ApiService;
use App\Repository\BaseObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommonController extends AbstractController
{
	const FIELDS_REQUEST = 'fields';
	const FIELDS_SEPARATOR_REQUEST = ',';

	public function __construct(
		private Security $security,
		private EntityManagerInterface $manager,
		private BaseObjectRepository $repository,
		private ApiService $service,
	)
	{
	}

	#[Route('/api/{id}', name: 'common_get', methods: ['GET'])]
	public function entity(string $id, Request $request): Response
	{
		if ($object = $this->getObject($id))
		{
			$this->denyAccessUnlessGranted('view',$object);
			return $this->json([
				'data' => $this->formatObject(
					entity: $this->service->buildApiEntityObject($object),
					fields: $this->getRequestedFields($request))
			]);
		}
		throw new BadRequestException();
	}

	protected function getObject(string $id): null|object
	{
		return $id === 'me' ? $this->getUser() : $this->repository->find($id);
	}

	protected function formatObject(ApiEntity $entity, array $fields): array
	{
		return array_reduce(
			$fields,
			function(array $result, string $field) use ($entity): array {
				$this->denyAccessUnlessGranted($field . '.view', $entity->getObject());
				$result[$field] = $entity->getProperty($field);
				return $result;
			},
			[]
		);
	}

	protected function getRequestedFields(Request $request): array|null
	{
		if ($raw = $request->query->get(static::FIELDS_REQUEST, default: null))
		{
			return array_map('trim', explode(static::FIELDS_SEPARATOR_REQUEST, $raw));
		}

		return null;
	}

	#[Route('/api/{id}', name: 'common_update', methods: ['POST'])]
	public function updateEntity(string $id, Request $request, ValidatorInterface $validator): Response
	{
		if ($object = $this->getObject($id))
		{
			$apiObject = $this->service->buildApiEntityObject($object);
			$content = $this->getContent($request->getContent()) + $request->files->all();
			$keys = [];
			foreach ($content as $key => $value)
			{
				$this->denyAccessUnlessGranted($key . '.update', $apiObject->getObject());
				$apiObject->setProperty($key, $value);
				$keys[] = $key;
			}
			if (count($errors = $validator->validate($object)) === 0)
			{
				$this->manager->flush();

				return $this->json([
					'data' => $this->formatObject($apiObject, $keys)
				]);
			}

			return $this->json([
				'error' => (string)$errors
			],
				Response::HTTP_NOT_FOUND);
		}

	}

	private function getContent(string $content): ?array
	{
		if ($content <> "" && !$data = json_decode($content, true))
		{
			if (json_last_error() !== JSON_ERROR_NONE)
			{
				throw new BadRequestHttpException('invalid json body: '. json_last_error_msg());
			}
		}

		return $data ?? [];
	}

	#[Route('/api/{id}/{reference}', name: 'common_ref', methods: ['GET'])]
	public function reference(string $id, string $reference, Request $request): Response
	{
		if ($object = $this->getObject($id))
		{
			$fields = $this->getRequestedFields(request: $request);
			if ($reference = $this->service->buildApiEntityObject($object)->reference($reference))
			{
				if (is_array($reference))
				{
					return $this->json([
						'data' => array_map(
							function($entity) use ($fields) {
								return $this->formatObject($entity, $fields);
							},
							$reference
						)
					]);
				}

				return $this->json([
					'data' => $this->formatObject(entity: $reference, fields: $fields)
				]);
			}
			throw new BadRequestException('tut');
		}
		throw new BadRequestException('kek');
	}

	#[Route('/api/{id}/{method}', name: 'method', methods: ['POST'])]
	public function method(string $id, string $method, Request $request): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$this->denyAccessUnlessGranted($method . 'execute',$object);
			return $this->json([
				'data' => $this->service->buildApiEntityObject($object)->method(
					$method,
					$this->getContent($request)
				)
			]);
		}
		throw new BadRequestException("govno");
	}
}
