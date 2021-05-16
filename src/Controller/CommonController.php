<?php

namespace App\Controller;

use App\Api\Access;
use App\Api\ApiService;
use App\Api\Serializer\Serializer;
use App\Configurators\Exception\ValidationException;
use App\Repository\BaseObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommonController extends AbstractController
{
	use ApiTrait;

	/**
	 * CommonController constructor.
	 *
	 * @param Security $security
	 * @param EntityManagerInterface $manager
	 * @param BaseObjectRepository $repository
	 * @param ApiService $service
	 */
	public function __construct(
		private Security $security,
		private EntityManagerInterface $manager,
		private BaseObjectRepository $repository,
		private ApiService $service,
	)
	{
	}

	/**
	 * @param string $id
	 *
	 * @return false|object|null
	 */
	protected function getObject(string $id): null|false|object
	{
		return $id === 'me' ? $this->getUser() : $this->repository->findById($id);
	}

	/**
	 * @param string $id
	 * @param Serializer $serializer
	 * @param Access $access
	 *
	 * @return Response
	 * @throws \Exception
	 */
	#[Route('/api/{id}', name: 'common_get', methods: ['GET'])]
	public function entity(string $id, Serializer $serializer, Access $access): Response
	{
		if (($object = $this->getObject($id)) && $object = $this->service->buildApiEntityObject($object))
		{
			$access->denyAccessUnlessGranted('view',$object);

			return $this->json([
				'data' => $serializer->serialize($object)
			]);
		}
		throw $this->createNotFoundException("object $id not found");
	}

	/**
	 * @param string $id
	 *
	 * @return JsonResponse
	 */
	#[Route('/api/{id}', name: 'common_delete', methods: ['DELETE'])]
	public function deleteEntity(string $id): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$this->denyAccessUnlessGranted('delete', $object);
			$object->delete();
			$this->manager->flush();
			return $this->json([
				'data' => 'OK'
			]);
		}
		throw $this->createNotFoundException("object $id not found");
	}

	/**
	 * @param string $id
	 * @param Request $request
	 * @param ValidatorInterface $validator
	 * @param Serializer $serializer
	 * @param Access $access
	 *
	 * @return JsonResponse
	 * @throws ValidationException
	 */
	#[Route('/api/{id}', name: 'common_update', methods: ['POST'])]
	public function updateEntity(
		string $id,
		Request $request,
		ValidatorInterface $validator,
		Serializer $serializer,
		Access $access
	): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$fields = []; $content = $this->getContent($request);
			$access->denyAccessUnlessGranted('update', $apiObject = $this->service->buildApiEntityObject($object));
			foreach ($content as $field => $value)
			{
				$apiObject->setProperty($fields[] = $field, $value);
			}
			if (count($errors = $validator->validate($apiObject->getObject(), groups: "update")) === 0)
			{
				$this->manager->flush();
				return $this->json([
					'data' => $serializer->serializeObject($apiObject,$fields)
				]);
			}
			throw new ValidationException($errors);
		}
		throw $this->createNotFoundException("object $id not found");
	}

	/**
	 * @param string $id
	 * @param string $reference
	 * @param Request $request
	 * @param Serializer $serializer
	 * @param Access $access
	 *
	 * @return JsonResponse
	 * @throws \Exception
	 */
	#[Route('/api/{id}/{reference}', name: 'common_ref', methods: ['GET'])]
	public function reference(
		string $id,
		string $reference,
		Request $request,
		Serializer $serializer,
		Access $access
	): JsonResponse
	{
		if ($object = $this->getObject($id))
		{
			$access->denyAccessUnlessGranted('view',$apiEntity = $this->service->buildApiEntityObject($object));
			if ($referenceObjects = $apiEntity->reference($reference,$request->query->all()))
			{
				$access->denyAccessUnlessGranted('view',$referenceObjects);
				return $this->json([
					'data' => $serializer->serialize($referenceObjects)
				]);
			}
			throw $this->createNotFoundException("object $id/$reference not found");
		}
		throw $this->createNotFoundException("object $id not found");
	}

	/**
	 * @param string $id
	 * @param string $method
	 * @param Request $request
	 * @param Access $access
	 *
	 * @return JsonResponse
	 * @throws \Exception
	 */
	#[Route('/api/{id}/{method}', name: 'method', methods: ['POST'])]
	public function method(string $id, string $method, Request $request, Access $access): JsonResponse
	{
		if (($object = $this->getObject($id)) && $object = $this->service->buildApiEntityObject($object))
		{
			$access->denyAccessUnlessGranted($method,$object);
			return $this->json([
				'data' => $object->method($method, $this->getContent($request))
			]);
		}
		throw $this->createNotFoundException("object $id not found");
	}
}