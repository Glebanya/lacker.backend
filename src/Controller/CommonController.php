<?php

namespace App\Controller;

use App\Api\ApiEntity;
use App\Api\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\BaseObjectRepository;


class CommonController extends AbstractController
{
    const FIELDS_REQUEST = 'fields';
    const FIELDS_SEPARATOR_REQUEST = ',';

    public function __construct(
        private Security $security,
        private EntityManagerInterface $manager,
        private BaseObjectRepository $repository,
        private ApiService $service
    )
    {}

    private function getContent(string $content) : ?array
    {
        if ($content <> "" && !$data = json_decode($content,true))
        {
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
            }
        }
        return $data ?? [];
    }

    protected function getRequestedFields(Request $request) : array|null
    {
        if ($raw = $request->query->get(static::FIELDS_REQUEST,default: null))
        {
            return array_map('trim',explode(static::FIELDS_SEPARATOR_REQUEST,$raw));
        }
        return null;
    }

    protected function getObject(string $id) : null|object
    {
        return ($object = $id === 'me'? $this->getUser() :$this->repository->find($id));
    }

    protected function formatObject(ApiEntity $entity, array $fields) : array
    {
        return array_map(
            function (string $field) use ($entity) : string {
                return $entity->property($field);
            },
            $fields
        );
    }

    #[Route('/api/{id}', name: 'common_get', methods: ['GET'])]
    public function entity(string $id,Request $request): Response
    {
        if ($object = $this->getObject($id))
        {
            return $this->json([
                'data' => $this->formatObject(
                 entity: $this->service->buildApiEntityObject($object),
                 fields: $this->getRequestedFields(request: $request)
                )
            ]);
        }
        throw new BadRequestException();
    }

    #[Route('/api/{id}/{ref}', name: 'common_ref', methods: ['GET'])]
    public function reference (string $id, string $reference, Request $request): Response
    {
        if ($object = $this->getObject($id))
        {
            $fields = $this->getRequestedFields(request: $request);
            if (is_array($ref = $this->service->buildApiEntityObject($object)->reference($reference)))
            {
                return $this->json([
                    'data' => array_map(
                        function ($entity) use ($fields) {
                            return $this->formatObject($entity,$fields);
                        },
                        $ref
                    )
                ]);
            }
            return $this->json([
                'data' => $this->formatObject(entity: $ref, fields: $fields)
            ]);
        }
        throw new BadRequestException("govno");
    }

    #[Route('/api/{id}/{method}', name: 'method', methods: ['POST'])]
    public function method(string $id, string $method, Request $request)
    {
        if ($object = $this->getObject($id))
        {
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
