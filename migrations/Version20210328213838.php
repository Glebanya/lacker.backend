<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210328213838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123FA89DB457');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392A89DB457');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, restaurant_id INT NOT NULL, status VARCHAR(255) NOT NULL, positions JSON NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), INDEX IDX_F5299398B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('DROP TABLE business');
        $this->addSql('DROP TABLE restaurant_staff');
        $this->addSql('ALTER TABLE dish ADD restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_957D8CB8B1E7706E ON dish (restaurant_id)');
        $this->addSql('DROP INDEX IDX_EB95123FA89DB457 ON restaurant');
        $this->addSql('ALTER TABLE restaurant DROP business_id');
        $this->addSql('DROP INDEX IDX_426EF392A89DB457 ON staff');
        $this->addSql('ALTER TABLE staff CHANGE business_id restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_426EF392B1E7706E ON staff (restaurant_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE business (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE restaurant_staff (restaurant_id INT NOT NULL, staff_id INT NOT NULL, INDEX IDX_8734043D4D57CD (staff_id), INDEX IDX_8734043B1E7706E (restaurant_id), PRIMARY KEY(restaurant_id, staff_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE restaurant_staff ADD CONSTRAINT FK_8734043B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_staff ADD CONSTRAINT FK_8734043D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8B1E7706E');
        $this->addSql('DROP INDEX IDX_957D8CB8B1E7706E ON dish');
        $this->addSql('ALTER TABLE dish DROP restaurant_id');
        $this->addSql('ALTER TABLE restaurant ADD business_id INT NOT NULL');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123FA89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        $this->addSql('CREATE INDEX IDX_EB95123FA89DB457 ON restaurant (business_id)');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392B1E7706E');
        $this->addSql('DROP INDEX IDX_426EF392B1E7706E ON staff');
        $this->addSql('ALTER TABLE staff CHANGE restaurant_id business_id INT NOT NULL');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        $this->addSql('CREATE INDEX IDX_426EF392A89DB457 ON staff (business_id)');
    }
}
