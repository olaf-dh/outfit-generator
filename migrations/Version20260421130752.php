<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421130752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothing_item ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E97E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CFE0A4E97E3C61F9 ON clothing_item (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E97E3C61F9');
        $this->addSql('DROP INDEX IDX_CFE0A4E97E3C61F9 ON clothing_item');
        $this->addSql('ALTER TABLE clothing_item DROP owner_id');
    }
}
