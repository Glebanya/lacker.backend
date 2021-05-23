<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523131633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE base_user CHANGE avatar avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dish CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE type type TINYTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE menu CHANGE title title JSON NOT NULL, CHANGE description description JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD order_table_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ADD checked TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398D07CAED FOREIGN KEY (order_table_id) REFERENCES `table` (id)');
        $this->addSql('CREATE INDEX IDX_F5299398D07CAED ON `order` (order_table_id)');
        $this->addSql('ALTER TABLE portion CHANGE title title JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_order ADD drinks_immediately TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE base_user CHANGE avatar avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dish CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE menu CHANGE title title JSON NOT NULL, CHANGE description description JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398D07CAED');
        $this->addSql('DROP INDEX IDX_F5299398D07CAED ON `order`');
        $this->addSql('ALTER TABLE `order` DROP order_table_id, DROP checked');
        $this->addSql('ALTER TABLE portion CHANGE title title JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE sub_order DROP drinks_immediately');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }
}
