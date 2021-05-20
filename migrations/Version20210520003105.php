<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210520003105 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_order (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', base_order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', checked TINYINT(1) NOT NULL, count INT NOT NULL, INDEX IDX_196DF721C774CEC0 (base_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_order_portion (sub_order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', portion_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D95793B337308361 (sub_order_id), INDEX IDX_D95793B3162BE352 (portion_id), PRIMARY KEY(sub_order_id, portion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_order ADD CONSTRAINT FK_196DF721C774CEC0 FOREIGN KEY (base_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE sub_order ADD CONSTRAINT FK_196DF721BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_order_portion ADD CONSTRAINT FK_D95793B337308361 FOREIGN KEY (sub_order_id) REFERENCES sub_order (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_order_portion ADD CONSTRAINT FK_D95793B3162BE352 FOREIGN KEY (portion_id) REFERENCES portion (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE order_portion');
        $this->addSql('ALTER TABLE base_user ADD family_name VARCHAR(255) NOT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE dish ADD stopped TINYINT(1) NOT NULL, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE menu ADD tag VARCHAR(255) DEFAULT \'MINOR\' NOT NULL, CHANGE title title JSON NOT NULL, CHANGE description description JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD final_count INT NOT NULL, DROP comment, DROP currency, DROP sum');
        $this->addSql('ALTER TABLE portion ADD title JSON DEFAULT NULL, ADD sort INT DEFAULT 0 NOT NULL, CHANGE price price INT NOT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_order_portion DROP FOREIGN KEY FK_D95793B337308361');
        $this->addSql('CREATE TABLE order_portion (order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', portion_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_16FA546D162BE352 (portion_id), INDEX IDX_16FA546D8D9F6D38 (order_id), PRIMARY KEY(order_id, portion_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_portion ADD CONSTRAINT FK_16FA546D162BE352 FOREIGN KEY (portion_id) REFERENCES portion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_portion ADD CONSTRAINT FK_16FA546D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE sub_order');
        $this->addSql('DROP TABLE sub_order_portion');
        $this->addSql('ALTER TABLE base_user DROP family_name, CHANGE avatar avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE dish DROP stopped, CHANGE description description JSON NOT NULL, CHANGE name name JSON NOT NULL, CHANGE image image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE menu DROP tag, CHANGE title title JSON NOT NULL, CHANGE description description JSON NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD currency VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD sum JSON NOT NULL, DROP final_count');
        $this->addSql('ALTER TABLE portion DROP title, DROP sort, CHANGE price price JSON NOT NULL');
        $this->addSql('ALTER TABLE restaurant CHANGE name name JSON NOT NULL, CHANGE logo logo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `table` CHANGE title title JSON NOT NULL');
    }
}
