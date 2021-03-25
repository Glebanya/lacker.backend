<?php


namespace App\GraphQL\Resolver;

use App\Entity\Menu;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class PrivateMenuResolver extends AbstractResolver implements ResolverInterface
{

    public function resolve(int $id) : object|null
    {
        if ($portion = $this->manager->find(Menu::class,$id))
        {
            return $portion;
        }
        return null;
    }


    public function dishes(Menu $menu)
    {
        return $menu->getDishes();
    }

    public function name(Menu $menu)
    {
        return $menu->getName()['ru'];
    }

    public function description(Menu $menu)
    {
        return $menu->getDescription()['ru'];
    }

    public function id(Menu $menu)
    {
        return $menu->getId();
    }
}