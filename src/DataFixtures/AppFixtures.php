<?php

namespace App\DataFixtures;

use App\Entity\Dish;
use App\Entity\Menu;
use App\Entity\Restaurant;
use App\Entity\Staff;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	public function load(ObjectManager $manager)
	{
		$restaurant = (new Restaurant())->setName(['ru' => 'ООО Рога и Копыта'])->setCurrency('RUB');
		$restaurant->addMenu(new Menu([
				'title' => [
					'ru' => "Главное меню"
				],
				'description' => [
					'ru' => "Главное меню для пастофариан"
				],
				'tag' => Menu::MENU_TAG_MAIN
			])
		);

		for ($i = 0; $i < 5; $i++)
		{
			$restaurant->addTable(
				new Table([
					'persons' => $i + 1,
					'title' => ['ru' => "Стол № $i"],
					'status' => Table::STATUS_FREE,
				])
			);
		}

		$restaurant->addStaff(
			new Staff([
				'name' => 'Глеб',
				'family_name' => 'Алексеев',
				'email' => 'kek.bam@gmail.com',
				'avatar' => 'https://slovnet.ru/wp-content/uploads/2018/09/1-58.jpg',
				'role' => Staff::ROLE_ADMINISTRATOR,
				'password' => '11111111',
			])
		);
		$restaurant->addStaff(
			new Staff([
				'name' => 'Алексей',
				'family_name' => 'Скворцов',
				'email' => 'm.skvor@mail.ru',
				'avatar' => 'https://slovnet.ru/wp-content/uploads/2018/09/1-58.jpg',
				'role' => Staff::ROLE_MANAGER,
				'password' => '11111111',
			])
		);
		$restaurant->addStaff(
			new Staff([
				'name' => 'Полина',
				'family_name' => 'Чернова',
				'email' => 'pol.cher@mail.ru',
				'avatar' => 'https://slovnet.ru/wp-content/uploads/2018/09/1-58.jpg',
				'role' => Staff::ROLE_STAFF,
				'password' => '11111111',
			])
		);
		$restaurant->addStaff(
			new Staff([
				'name' => 'Артем',
				'family_name' => 'Багаутдинов',
				'email' => 'bagautdinov@mail.ru',
				'avatar' => 'https://slovnet.ru/wp-content/uploads/2018/09/1-58.jpg',
				'role' => Staff::ROLE_STAFF,
				'password' => '11111111',
			])
		);

		$manager->persist($restaurant);
		$manager->flush();
	}
}
