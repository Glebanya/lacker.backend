<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210525190036 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE appeal (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', appeal_table_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', target VARCHAR(255) NOT NULL, comment LONGTEXT DEFAULT NULL, checked TINYINT(1) NOT NULL, INDEX IDX_96794351B2CB9DA1 (appeal_table_id), INDEX IDX_96794351A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351B2CB9DA1 FOREIGN KEY (appeal_table_id) REFERENCES `table` (id)');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE appeal ADD CONSTRAINT FK_96794351BF396750 FOREIGN KEY (id) REFERENCES base_object (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE appeal');
    }
}
