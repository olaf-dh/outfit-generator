<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260418083236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pattern DROP name, DROP description');
        $this->addSql('ALTER TABLE season DROP name, DROP description');
        $this->addSql('ALTER TABLE style DROP name');
        $this->addSql('ALTER TABLE weather_condition DROP name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pattern ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE season ADD name VARCHAR(100) NOT NULL, ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE style ADD name VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE weather_condition ADD name VARCHAR(100) NOT NULL');
    }
}
