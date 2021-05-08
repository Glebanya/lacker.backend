<?php

namespace App\DataFixtures;

use App\Entity\Business;
use App\Entity\Dish;
use App\Entity\Menu;
use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Entity\Staff;
use App\Types\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	public function __construct(private EntityManagerInterface $manager, private UserPasswordEncoderInterface $encoder)
	{
	}

	public function load(ObjectManager $manager)
	{
		$restaurant = (new Restaurant())->setName(['ru' => 'ООО Рога и Копыта']);
		for ($i = 0; $i < 2; $i++)
		{
			$menu = (new Menu())->setTitle(['ru' => "Меню № $i"])->setDescription(['ru' => "Меню для всех"]);
			for( $j = 0; $j < 2; $j++ )
			{
				$dish = (new Dish())
					->setName(['ru' => "Блюдо № $j"])
					->setDescription(['ru' => 'Очень вкусное'])
					->setImage(new Image('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvnK2svdusyfTDEAhPXRIPxSXEAIjXMj2-fA&usqp=CAU'))
					->setType(Dish::TYPE_DISH);
				for ($k = 0; $k < 3 ; $k++)
				{
					$portion = (new Portion())->setPrice(['RUB' => $k*100 + 4.87])->setSize($k);
					$dish->addPortion($portion);
				}
				$menu->addDish($dish);
			}
			$restaurant->addMenu($menu);
		}
		$roles = [Staff::ROLE_ADMINISTRATOR,Staff::ROLE_MANAGER,Staff::ROLE_STAFF];
		foreach($roles as $role)
		{
			$staff = ($staff = new Staff())
				->setStatus(Staff::STATUS_WORKING)
				->setRoles($role)
				->setEmail('kek@mail.com')
				->setPassword($this->encoder->encodePassword($staff,'11111111'))
				->setAvatar(new Image('https://slovnet.ru/wp-content/uploads/2018/09/1-58.jpg'));
			$restaurant->addStaff($staff);
		}
		$manager->persist($restaurant);
		$manager->flush();
	}
}
