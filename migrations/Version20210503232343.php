<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503232343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE menu (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', restaurant_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title JSON NOT NULL, description JSON NOT NULL, INDEX IDX_7D053A93B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE table_reserve (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', __table_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(255) NOT NULL, INDEX IDX_1DD3AB2D8FA3CC21 (__table_id), INDEX IDX_1DD3AB2DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE table_reserve ADD CONSTRAINT FK_1DD3AB2D8FA3CC21 FOREIGN KEY (__table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE table_reserve ADD CONSTRAINT FK_1DD3AB2DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE table_reserve ADD CONSTRAINT FK_1DD3AB2DBF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE base_object ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE base_user ADD avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8B1E7706E');
        $this->addSql('DROP INDEX IDX_957D8CB8B1E7706E ON dish');
        $this->addSql('ALTER TABLE dish ADD menu_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', DROP restaurant_id, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('CREATE INDEX IDX_957D8CB8CCD7E912 ON dish (menu_id)');
        $this->addSql('ALTER TABLE `order` CHANGE sum sum JSON NOT NULL');
        $this->addSql('ALTER TABLE portion CHANGE price price JSON NOT NULL');
        $this->addSql('ALTER TABLE restaurant ADD logo VARCHAR(255) DEFAULT NULL, CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB8CCD7E912');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE table_reserve');
        $this->addSql('ALTER TABLE base_object DROP deleted');
        $this->addSql('ALTER TABLE base_user DROP avatar');
        $this->addSql('DROP INDEX IDX_957D8CB8CCD7E912 ON dish');
        $this->addSql('ALTER TABLE dish ADD restaurant_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', DROP menu_id, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB8B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_957D8CB8B1E7706E ON dish (restaurant_id)');
        $this->addSql('ALTER TABLE `order` CHANGE sum sum JSON NOT NULL');
        $this->addSql('ALTER TABLE portion CHANGE price price JSON NOT NULL');
        $this->addSql('ALTER TABLE restaurant DROP logo, CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }
}
