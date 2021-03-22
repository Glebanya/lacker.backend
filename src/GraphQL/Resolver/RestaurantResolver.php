<?php


namespace App\GraphQL\Resolver;

use App\Entity\Business;
use App\Entity\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class RestaurantResolver implements ResolverInterface
{
    public function __construct(
        public EntityManagerInterface $manager,
    )
    {}

    public function resolve(int $id) : object|null
    {
        if ($restaurant = $this->manager->find(Restaurant::class,$id))
        {
            return $restaurant;
        }
        return null;
    }

    public function __invoke(ResolveInfo $info, $value, Argument $args)
    {
        $method = $info->fieldName;
        return $this->$method($value, $args);
    }

    public function business(?Restaurant $restaurant)
    {
        return $restaurant->getBusiness();
    }

    public function menus(?Restaurant $restaurant)
    {
        return $restaurant->getMenus();
    }

    public function name(?Restaurant $restaurant)
    {
        return $restaurant->getName()['ru'];
    }

    public function id(Restaurant $dish)
    {
        return $dish->getId();
    }
}