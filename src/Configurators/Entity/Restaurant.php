<?php


namespace App\Configurators\Entity;

use App\Entity\Dish;
use App\Entity\Restaurant as RestaurantEntity,
    App\Api\ConfiguratorInterface;
use Doctrine\ORM\EntityManagerInterface;

class Restaurant extends BaseConfigurator implements ConfiguratorInterface
{
    protected const ENTITY_CLASS = RestaurantEntity::class;

    public function __construct(protected EntityManagerInterface $manager)
    {}

    protected function getMethods(): array
    {
        return [
            'remove_dish' => function (RestaurantEntity $object, array $params) {
                if (array_key_exists('dish',$params))
                {
                    if ($dish = $this->manager->getPartialReference(Dish::class,$params['dish']))
                    {
                        $object->removeDish($dish);
                        $this->manager->flush();
                    }
                }
                return true;
            },
            'remove_dish_batch' => function(RestaurantEntity $object, array $params) {

            },
            'add_dish' => function(RestaurantEntity $object, array $params) {

            },
            'add_dish_batch' => function(RestaurantEntity $object, array $params){

            },
            'add_staff' => function(RestaurantEntity $object, array $params){

            },
            'remove_staff' => function(RestaurantEntity $object, array $params){

            },
            'add_staff_batch' => function(RestaurantEntity $object, array $params){

            },
        ];
    }
}