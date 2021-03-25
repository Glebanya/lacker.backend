<?php


namespace App\GraphQL\Mutation;

use Doctrine\ORM\EntityManagerInterface;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;

class PortionMutation implements MutationInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {}

    public function create()
    {

    }
}