<?php


namespace App\GraphQL\Resolver;

use App\Entity\Business;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PrivateBusinessResolver extends AbstractResolver implements ResolverInterface
{


    public function resolve(int $id) : object|null
    {
        if ($portion = $this->manager->find(Business::class,$id))
        {
            return $portion;
        }
        return null;
    }

    public function staff(Business $business)
    {
        return $business->getStaff();
    }

    public function name(Business $business)
    {
        return $business->getName();
    }

    public function restaurants(Business $business)
    {
        return $business->getRestaurants();
    }

    public function id(Business $business)
    {
        return $business->getId();
    }
}