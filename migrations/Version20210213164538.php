<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210213164538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE business (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_client` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', full_name VARCHAR(255) NOT NULL, create_date DATETIME NOT NULL, update_date DATETIME NOT NULL, sex VARCHAR(255) NOT NULL, bithday DATE NOT NULL, mail VARCHAR(255) NOT NULL, phone VARCHAR(15) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_dish` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', menu_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_date DATETIME NOT NULL, update_date DATETIME NOT NULL, enable TINYINT(1) NOT NULL, INDEX IDX_761D1FE7CCD7E912 (menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_dish_portion` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', dish_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', enable TINYINT(1) NOT NULL, INDEX IDX_4591D82E148EB0CB (dish_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_hall` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F8EF3B60B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_menu` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', creation_date DATETIME NOT NULL, update_date DATETIME NOT NULL, enable TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_restaurant (menu_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_CA38A6EDCCD7E912 (menu_id), INDEX IDX_CA38A6EDB1E7706E (restaurant_id), PRIMARY KEY(menu_id, restaurant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_order` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', client_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', create_date DATETIME NOT NULL, status VARCHAR(1) NOT NULL, currency_type VARCHAR(15) NOT NULL, INDEX IDX_E1EBF6E19EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_dish_portion (order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', dish_portion_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_74F773DA8D9F6D38 (order_id), INDEX IDX_74F773DAA9DFB74 (dish_portion_id), PRIMARY KEY(order_id, dish_portion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_restaurant` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', business_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', timezone VARCHAR(127) NOT NULL, INDEX IDX_10696C27A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `lacker_table` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hall_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', max_persons INT NOT NULL, status VARCHAR(1) NOT NULL, number INT NOT NULL, INDEX IDX_D1EA3B052AFCFD6 (hall_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_resource_settings (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', value VARCHAR(127) NOT NULL, type LONGTEXT NOT NULL, INDEX IDX_D35588A8B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_resource_text (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', lang_type VARCHAR(127) NOT NULL, value LONGTEXT NOT NULL, type VARCHAR(127) NOT NULL, INDEX IDX_428A756FB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE symfony_session (session_id VARCHAR(255) NOT NULL, session_data LONGBLOB DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, end_of_life DATETIME NOT NULL, PRIMARY KEY(session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `lacker_dish` ADD CONSTRAINT FK_761D1FE7CCD7E912 FOREIGN KEY (menu_id) REFERENCES `lacker_menu` (id)');
        $this->addSql('ALTER TABLE `lacker_dish_portion` ADD CONSTRAINT FK_4591D82E148EB0CB FOREIGN KEY (dish_id) REFERENCES `lacker_dish` (id)');
        $this->addSql('ALTER TABLE `lacker_hall` ADD CONSTRAINT FK_F8EF3B60B1E7706E FOREIGN KEY (restaurant_id) REFERENCES `lacker_restaurant` (id)');
        $this->addSql('ALTER TABLE menu_restaurant ADD CONSTRAINT FK_CA38A6EDCCD7E912 FOREIGN KEY (menu_id) REFERENCES `lacker_menu` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_restaurant ADD CONSTRAINT FK_CA38A6EDB1E7706E FOREIGN KEY (restaurant_id) REFERENCES `lacker_restaurant` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `lacker_order` ADD CONSTRAINT FK_E1EBF6E19EB6921 FOREIGN KEY (client_id) REFERENCES `lacker_client` (id)');
        $this->addSql('ALTER TABLE order_dish_portion ADD CONSTRAINT FK_74F773DA8D9F6D38 FOREIGN KEY (order_id) REFERENCES `lacker_order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_dish_portion ADD CONSTRAINT FK_74F773DAA9DFB74 FOREIGN KEY (dish_portion_id) REFERENCES `lacker_dish_portion` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `lacker_restaurant` ADD CONSTRAINT FK_10696C27A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        $this->addSql('ALTER TABLE `lacker_table` ADD CONSTRAINT FK_D1EA3B052AFCFD6 FOREIGN KEY (hall_id) REFERENCES `lacker_hall` (id)');
        $this->addSql('ALTER TABLE restaurant_resource_settings ADD CONSTRAINT FK_D35588A8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES `lacker_restaurant` (id)');
        $this->addSql('ALTER TABLE restaurant_resource_text ADD CONSTRAINT FK_428A756FB1E7706E FOREIGN KEY (restaurant_id) REFERENCES `lacker_restaurant` (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `lacker_restaurant` DROP FOREIGN KEY FK_10696C27A89DB457');
        $this->addSql('ALTER TABLE `lacker_order` DROP FOREIGN KEY FK_E1EBF6E19EB6921');
        $this->addSql('ALTER TABLE `lacker_dish_portion` DROP FOREIGN KEY FK_4591D82E148EB0CB');
        $this->addSql('ALTER TABLE order_dish_portion DROP FOREIGN KEY FK_74F773DAA9DFB74');
        $this->addSql('ALTER TABLE `lacker_table` DROP FOREIGN KEY FK_D1EA3B052AFCFD6');
        $this->addSql('ALTER TABLE `lacker_dish` DROP FOREIGN KEY FK_761D1FE7CCD7E912');
        $this->addSql('ALTER TABLE menu_restaurant DROP FOREIGN KEY FK_CA38A6EDCCD7E912');
        $this->addSql('ALTER TABLE order_dish_portion DROP FOREIGN KEY FK_74F773DA8D9F6D38');
        $this->addSql('ALTER TABLE `lacker_hall` DROP FOREIGN KEY FK_F8EF3B60B1E7706E');
        $this->addSql('ALTER TABLE menu_restaurant DROP FOREIGN KEY FK_CA38A6EDB1E7706E');
        $this->addSql('ALTER TABLE restaurant_resource_settings DROP FOREIGN KEY FK_D35588A8B1E7706E');
        $this->addSql('ALTER TABLE restaurant_resource_text DROP FOREIGN KEY FK_428A756FB1E7706E');
        $this->addSql('DROP TABLE business');
        $this->addSql('DROP TABLE `lacker_client`');
        $this->addSql('DROP TABLE `lacker_dish`');
        $this->addSql('DROP TABLE `lacker_dish_portion`');
        $this->addSql('DROP TABLE `lacker_hall`');
        $this->addSql('DROP TABLE `lacker_menu`');
        $this->addSql('DROP TABLE menu_restaurant');
        $this->addSql('DROP TABLE `lacker_order`');
        $this->addSql('DROP TABLE order_dish_portion');
        $this->addSql('DROP TABLE `lacker_restaurant`');
        $this->addSql('DROP TABLE `lacker_table`');
        $this->addSql('DROP TABLE restaurant_resource_settings');
        $this->addSql('DROP TABLE restaurant_resource_text');
        $this->addSql('DROP TABLE symfony_session');
    }
}
