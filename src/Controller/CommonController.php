<?php

namespace App\Controller;

use App\Api\ApiService;
use App\Repository\BaseObjectRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommonController extends AbstractController
{
	use ApiTrait;

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
					fields: $this->getRequestedFields($request)
				)
			]);
		}
		throw new BadRequestException("object $id not found");
	}

	protected function getObject(string $id): null|false|object
	{
		return $id === 'me' ? $this->getUser() : $this->repository->matching(
			Criteria::create()
				->where(Criteria::expr()->eq('id',$id))
				->andWhere(Criteria::expr()->eq('deleted',false))
			)->first();
	}

	#[Route('/api/{id}', name: 'common_delete', methods: ['DELETE'])]
	public function deleteEntity(string $id): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$object->delete();
			$this->manager->flush();
			return $this->json([
				'data' => 'OK'
			]);
		}
		return $this->json([
		],Response::HTTP_NOT_FOUND);
	}

	#[Route('/api/{id}', name: 'common_update', methods: ['POST'])]
	public function updateEntity(string $id, Request $request, ValidatorInterface $validator): Response
	{
		if ($object = $this->getObject($id))
		{
			$this->denyAccessUnlessGranted('update', $object);
			$apiObject = $this->service->buildApiEntityObject($object); $content = $this->getContent($request);	$keys = [];
			foreach ($content as $key => $value)
			{
				$apiObject->setProperty($keys[] = $key, $value);
			}
			if (count($errors = $validator->validate($apiObject->getObject(), groups: "update")) === 0)
			{
				$this->manager->flush();
				return $this->json([
					'data' => $this->formatObject($apiObject, $keys)
				]);
			}

			return $this->json([
				'error' => (string) $errors
			],
				Response::HTTP_NOT_FOUND);
		}
		throw new BadRequestException("object $id not found");
	}

	#[Route('/api/{id}/{reference}', name: 'common_ref', methods: ['GET'])]
	public function reference(string $id, string $reference, Request $request): Response
	{
		if ($object = $this->getObject($id))
		{
			$fields = $this->getRequestedFields(request: $request);
			if ($reference = $this->service->buildApiEntityObject($object)->reference($reference,$request->query->all()))
			{
				if (is_array($reference))
				{
					return $this->json([
						'data' => array_map(
							function($entity) use ($fields) {
								$this->denyAccessUnlessGranted('view',$entity->getObject());
								return $this->formatObject($entity, $fields);
							},
							$reference
						)
					]);
				}
				$this->denyAccessUnlessGranted('view', $reference->getObject());
				return $this->json([
					'data' => $this->formatObject(entity: $reference, fields: $fields)
				]);
			}
			throw new BadRequestException("object $id not found");
		}
		throw new BadRequestException("object $id not found");
	}

	#[Route('/api/{id}/{method}', name: 'method', methods: ['POST'])]
	public function method(string $id, string $method, Request $request): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$this->denyAccessUnlessGranted($method,$object);
			return $this->json([
				'data' => $this->service->buildApiEntityObject($object)->method($method, $this->getContent($request))
			]);
		}
		throw new BadRequestException("object $id not found");
	}
}
