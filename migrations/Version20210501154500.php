<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501154500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE base_object (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', crate_date DATETIME NOT NULL, update_date DATETIME NOT NULL, entity_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE base_user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1BF018B9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dish (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', description JSON NOT NULL, name JSON NOT NULL, INDEX IDX_957D8CB8B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, currency VARCHAR(255) NOT NULL, sum JSON NOT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F5299398B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_portion (order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', portion_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_16FA546D8D9F6D38 (order_id), INDEX IDX_16FA546D162BE352 (portion_id), PRIMARY KEY(order_id, portion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE portion (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', dish_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', price JSON NOT NULL, size JSON NOT NULL, INDEX IDX_E080FD26148EB0CB (dish_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE staff (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', firebase_token VARCHAR(255) DEFAULT NULL, INDEX IDX_426EF392B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `table` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, persons INT NOT NULL, title JSON NOT NULL, INDEX IDX_F6298F46B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', google_id VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D64976F5C865 (google_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE base_user ADD CONSTRAINT FK_1BF018B9BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_portion ADD CONSTRAINT FK_16FA546D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_portion ADD CONSTRAINT FK_16FA546D162BE352 FOREIGN KEY (portion_id) REFERENCES portion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE portion ADD CONSTRAINT FK_E080FD26148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id)');
        $this->addSql('ALTER TABLE portion ADD CONSTRAINT FK_E080FD26BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123FBF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE `table` ADD CONSTRAINT FK_F6298F46BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE base_user DROP FOREIGN KEY FK_1BF018B9BF396750');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8BF396750');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398BF396750');
        $this->addSql('ALTER TABLE portion DROP FOREIGN KEY FK_E080FD26BF396750');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123FBF396750');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392BF396750');
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46BF396750');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BF396750');
        $this->addSql('ALTER TABLE portion DROP FOREIGN KEY FK_E080FD26148EB0CB');
        $this->addSql('ALTER TABLE order_portion DROP FOREIGN KEY FK_16FA546D8D9F6D38');
        $this->addSql('ALTER TABLE order_portion DROP FOREIGN KEY FK_16FA546D162BE352');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8B1E7706E');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398B1E7706E');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392B1E7706E');
        $this->addSql('ALTER TABLE `table` DROP FOREIGN KEY FK_F6298F46B1E7706E');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP TABLE base_object');
        $this->addSql('DROP TABLE base_user');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_portion');
        $this->addSql('DROP TABLE portion');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE staff');
        $this->addSql('DROP TABLE `table`');
        $this->addSql('DROP TABLE user');
    }
}
