<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504153843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_665648E95E237E06 ON color (name)');
        $this->addSql('ALTER TABLE material ADD category VARCHAR(255) NOT NULL, ADD warmth VARCHAR(255) NOT NULL, ADD breathability VARCHAR(255) NOT NULL, ADD waterproof TINYINT NOT NULL, ADD stretch TINYINT NOT NULL, ADD windproof TINYINT NOT NULL, DROP description, DROP is_warm');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_665648E95E237E06 ON color');
        $this->addSql('ALTER TABLE material ADD description LONGTEXT DEFAULT NULL, ADD is_warm TINYINT DEFAULT 0 NOT NULL, DROP category, DROP warmth, DROP breathability, DROP waterproof, DROP stretch, DROP windproof');
    }
}
