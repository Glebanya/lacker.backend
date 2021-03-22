<?php


namespace App\GraphQL\Resolver;

use App\Entity\Portion;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PortionResolver implements ResolverInterface
{
    public function __construct(
        public EntityManagerInterface $manager,
    )
    {}

    public function resolve(int $id) : object|null
    {
        if ($portion = $this->manager->find(Portion::class,$id))
        {
            return $portion;
        }
        return null;
    }

    public function __invoke(ResolveInfo $info, $value, Argument $args)
    {
        $method = $info->fieldName;
        return $this->$method($value, $args);
    }

    public function size(?Portion $portion)
    {
        return $portion->getSize()['ru'];
    }

    public function price(?Portion $portion)
    {
        return $portion->getPrice()['ru'];
    }

    public function id(Portion $dish)
    {
        return $dish->getId();
    }
}