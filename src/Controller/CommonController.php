<?php

namespace App\Controller;

use App\API\ApiObject;
use App\API\ObjectWrapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\BaseObjectRepository;


class CommonController extends AbstractController
{
    protected function getSeparator() : string
    {
        return ',';
    }
    protected function getFieldQueryName() : string
    {
        return 'fields';
    }

    protected function getRequestedFields(Request $request) : array|null
    {
        if (
            $request->query->has(key:$name = $this->getFieldQueryName())
            && $raw = $request->query->get(key: $this->getFieldQueryName(),default: '')
        )
        {
            return explode($this->getSeparator(),$raw);
        }
        return null;
    }

    public function __construct(
        private Security $security,
        private EntityManagerInterface $manager,
        private BaseObjectRepository $repository,
    )
    {}

    #[Route('/api/{id}', name: 'common_get', methods: ['GET'])]
    public function getEntity(string $id,Request $request): Response
    {
        if ($object = $this->repository->find($id))
        {
            $wrapper = new ObjectWrapper($object);
            $fields = $this->getRequestedFields(request: $request,);
            return $this->json([
                'data' => $wrapper->getFields($fields)
            ]);
        }
        throw new BadRequestException();
    }

    #[Route('/api/{id}/{ref}', name: 'common_get', methods: ['GET'])]
    public function getRef(string $id,string $ref,Request $request): Response
    {
        if ($object = $this->repository->find($id))
        {
            $wrapper = new ObjectWrapper($object);
            $fields = $this->getRequestedFields(request: $request,);
            return $this->json([
                'data' => $wrapper->getReference($ref,$fields)
            ]);
        }
        throw new BadRequestException();
    }

}
