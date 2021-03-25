<?php


namespace App\GraphQL\Resolver;

use App\Entity\Staff;
use GraphQL\Type\Definition\ResolveInfo;
use JetBrains\PhpStorm\Pure;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PrivateStaffResolver  extends AbstractResolver implements ResolverInterface
{

    public function resolve(int $id) : object|null
    {
        if ($staff = $this->manager->find(Staff::class,$id))
        {
            return $staff;
        }
        return null;
    }

    public function business(Staff $staff)
    {
        return $staff->getBusiness();
    }

    #[Pure] public function name(Staff $staff)
    {
        return $staff->getUsername();
    }

    #[Pure] public function email(Staff $staff)
    {
        return $staff->getEmail();
    }

    #[Pure] public function roles(Staff $staff)
    {
        return $staff->getRoles();
    }

    #[Pure] public function restaurants(Staff $staff)
    {
        return $staff->getRestaurants();
    }

    #[Pure] public function id(Staff $dish)
    {
        return $dish->getId();
    }
}