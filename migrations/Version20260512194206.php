<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512194206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE clothing_item_pattern DROP FOREIGN KEY `FK_858EB04CAA13B545`');
        $this->addSql('ALTER TABLE clothing_item_pattern DROP FOREIGN KEY `FK_858EB04CF734A20F`');
        $this->addSql('DROP TABLE clothing_item_pattern');
        $this->addSql('ALTER TABLE clothing_item ADD pattern_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E9F734A20F FOREIGN KEY (pattern_id) REFERENCES pattern (id)');
        $this->addSql('CREATE INDEX IDX_CFE0A4E9F734A20F ON clothing_item (pattern_id)');
        $this->addSql('ALTER TABLE pattern ADD max_secondary_color INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clothing_item_pattern (clothing_item_id INT NOT NULL, pattern_id INT NOT NULL, INDEX IDX_858EB04CAA13B545 (clothing_item_id), INDEX IDX_858EB04CF734A20F (pattern_id), PRIMARY KEY (clothing_item_id, pattern_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE clothing_item_pattern ADD CONSTRAINT `FK_858EB04CAA13B545` FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_pattern ADD CONSTRAINT `FK_858EB04CF734A20F` FOREIGN KEY (pattern_id) REFERENCES pattern (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E9F734A20F');
        $this->addSql('DROP INDEX IDX_CFE0A4E9F734A20F ON clothing_item');
        $this->addSql('ALTER TABLE clothing_item DROP pattern_id');
        $this->addSql('ALTER TABLE pattern DROP max_secondary_color');
    }
}
