<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417205002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD body_zone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE clothing_item DROP min_layer_depth, DROP max_layer_depth, DROP body_zone');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP body_zone');
        $this->addSql('ALTER TABLE clothing_item ADD min_layer_depth INT NOT NULL, ADD max_layer_depth INT NOT NULL, ADD body_zone VARCHAR(255) NOT NULL');
    }
}
