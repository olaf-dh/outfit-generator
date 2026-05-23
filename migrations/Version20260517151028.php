<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517151028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE color ADD h DOUBLE PRECISION NOT NULL, ADD s DOUBLE PRECISION NOT NULL, ADD v DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE color ADD r SMALLINT NOT NULL, ADD g SMALLINT NOT NULL, ADD b SMALLINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE color DROP h, DROP s, DROP v');
        $this->addSql('ALTER TABLE color DROP r, DROP g, DROP b');
    }
}
