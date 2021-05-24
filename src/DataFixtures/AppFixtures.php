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
	public function __construct(private EntityManagerInterface $manager, private UserPasswordEncoderInterface $encoder)
	{
	}

	public function load(ObjectManager $manager)
	{
		$restaurant = (new Restaurant())
			->setName(['ru' => 'ООО Рога и Копыта corp'])
			->setCurrency('RUB');
		for ($i = 0; $i < 10; $i++)
		{
			$restaurant->addMenu(new Menu([
					'title' => [
						'ru' => $i === 0 ? "Главное меню" : "Меню № $i"
					],
					'description' => [
						'ru' => $i === 0 ? "Главное меню для пастофариан" : "Меню для детей"
					],
					'tag' => $i === 0 ? Menu::MENU_TAG_MAIN : Menu::MENU_TAG_MINOR,
					'dishes' => array_map(fn(int $j) => [
						'title' => [
							'ru' => "Блюдо № $j"
						],
						'description' => [
							'ru' => "Внатуре четко"
						],
						'tags' => [Dish::getTypes()[$i], Dish::getTypes()[$i+1]],
						'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvnK2svdusyfTDEAhPXRIPxSXEAIjXMj2-fA&usqp=CAU',
						'portions' => array_map(fn(int $k) => [
							'sort' => $k * 100,
							'weight' => $k * 100 + 100,
							'price' => $k * 100 + 99,
							'title' => [
								'ru' => $k === 2 ? "Большой" : ($k === 1 ? "Средний" : "Маленький")
							]
						],
							range(0, 2, 1))
					],
						range(0, 10, 1))
				])
			);
		}

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
