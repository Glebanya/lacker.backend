<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210222105349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `lacker_client` ADD COLUMN `google_id` VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `phone` `phone` VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `sex` `sex` VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `bithday` `bithday` DATE DEFAULT NULL');

    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE `lacker_client` DROP COLUMN `google_id`');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `phone` `phone` VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `sex` `sex` VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `lacker_client` CHANGE COLUMN `bithday` `bithday` DATE NOT NULL');
    }
}
