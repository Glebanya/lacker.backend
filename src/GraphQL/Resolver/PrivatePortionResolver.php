<?php


namespace App\GraphQL\Resolver;

use App\Entity\Portion;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PrivatePortionResolver extends AbstractResolver implements ResolverInterface
{

    public function resolve(int $id) : object|null
    {
        if ($portion = $this->manager->find(Portion::class,$id))
        {
            return $portion;
        }
        return null;
    }

    public function size(Portion $portion)
    {
        return $portion->getSize()['ru'];
    }

    public function price(Portion $portion)
    {
        return $portion->getPrice()['ru'];
    }

    public function id(Portion $dish)
    {
        return $dish->getId();
    }
}