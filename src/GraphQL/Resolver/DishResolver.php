<?php


namespace App\GraphQL\Resolver;

use App\Entity\Dish;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class DishResolver implements ResolverInterface
{
    public function __construct(
        public EntityManagerInterface $manager,
    )
    {}

    public function resolve(int $id) : object|null
    {
        if ($portion = $this->manager->find(Dish::class,$id))
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

    public function portions(Dish $menu)
    {
        return $menu->getPortions();
    }

    public function name(Dish $menu)
    {
        return $menu->getName()['ru'];
    }

    public function description(Dish $menu)
    {
        return $menu->getDescription()['ru'];
    }

    public function id(Dish $menu)
    {
        return $menu->getId();
    }
}