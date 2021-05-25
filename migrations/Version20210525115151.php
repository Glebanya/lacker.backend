<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210525115151 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$this->addSql('ALTER TABLE sub_order_portion DROP PRIMARY KEY');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
		$this->addSql('ALTER TABLE sub_order_portion DROP FOREIGN KEY FK_D95793B337308361');
		$this->addSql('DROP TABLE sub_order_portion');
		$this->addSql('CREATE TABLE sub_order_portion (sub_order_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', portion_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_D95793B337308361 (sub_order_id), INDEX IDX_D95793B3162BE352 (portion_id), PRIMARY KEY(sub_order_id, portion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
		$this->addSql('ALTER TABLE sub_order_portion ADD CONSTRAINT FK_D95793B337308361 FOREIGN KEY (sub_order_id) REFERENCES sub_order (id) ON DELETE CASCADE');
		$this->addSql('ALTER TABLE sub_order_portion ADD CONSTRAINT FK_D95793B3162BE352 FOREIGN KEY (portion_id) REFERENCES portion (id) ON DELETE CASCADE');
    }
}
