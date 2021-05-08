<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210501180458 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dish ADD image VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(255) DEFAULT NULL, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE currency currency VARCHAR(255) DEFAULT NULL, CHANGE sum sum JSON NOT NULL');
        $this->addSql('ALTER TABLE portion ADD weight INT NOT NULL, DROP size, CHANGE price price JSON NOT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE dish DROP image, DROP type, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE currency currency VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE sum sum JSON NOT NULL');
        $this->addSql('ALTER TABLE portion ADD size JSON NOT NULL, DROP weight, CHANGE price price JSON NOT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }
}
