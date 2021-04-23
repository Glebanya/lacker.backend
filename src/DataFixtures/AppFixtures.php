<?php

namespace App\DataFixtures;

use App\Entity\Business;
use App\Entity\Dish;
use App\Entity\Menu;
use App\Entity\Portion;
use App\Entity\Restaurant;
use App\Entity\Staff;
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
		$roles = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_STAFF', 'ROLE_MANAGER', 'ROLE_STAFF'];
		for ($i = 0; $i < 5; $i++)
		{
			$rest = (new Restaurant())->setName(['ru' => 'cum'.$i]);
			$manager->persist($rest);
			for ($j = 0; $j < 5; $j++)
			{
				$mgn = ($staff = new Staff())->setUsername($i.'lol'.$j)->setRoles([$roles[$j]])
					->setPassword($this->encoder->encodePassword($staff, 'kek'))->setEmail('kek'.$i.$j.'@mail.com');
				$rest->addStaff($mgn);
				$manager->persist($mgn);
			}
			for ($j = 0; $j < 5; $j++)
			{
				for ($k = 0; $k < 5; $k++)
				{
					$dish = (new Dish())->setName(['ru' => 'имя'.$j])->setDescription(['ru' => 'описания'.$j])
						->setRestaurant($rest);
					$manager->persist($dish);
					for ($l = 0; $l < 3; $l++)
					{
						$port = (new Portion())->setDish($dish)->setPrice(['ru' => $l.'руб'])->setSize([
								'ru' => $l.
									'кг'
							]);
						$manager->persist($port);
					}
				}
			}
		}
		$manager->flush();
	}
}
