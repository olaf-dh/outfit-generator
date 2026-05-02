<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260414164337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE body_zone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clothing_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, min_layer_depth INT NOT NULL, max_layer_depth INT NOT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, sub_category_id INT DEFAULT NULL, body_zone_id INT DEFAULT NULL, INDEX IDX_CFE0A4E9F7BFE87C (sub_category_id), INDEX IDX_CFE0A4E976DA863D (body_zone_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clothing_item_season (clothing_item_id INT NOT NULL, season_id INT NOT NULL, INDEX IDX_1B983599AA13B545 (clothing_item_id), INDEX IDX_1B9835994EC001D1 (season_id), PRIMARY KEY (clothing_item_id, season_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clothing_item_style (clothing_item_id INT NOT NULL, style_id INT NOT NULL, INDEX IDX_41E5B7AFAA13B545 (clothing_item_id), INDEX IDX_41E5B7AFBACD6074 (style_id), PRIMARY KEY (clothing_item_id, style_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE clothing_item_pattern (clothing_item_id INT NOT NULL, pattern_id INT NOT NULL, INDEX IDX_858EB04CAA13B545 (clothing_item_id), INDEX IDX_858EB04CF734A20F (pattern_id), PRIMARY KEY (clothing_item_id, pattern_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, hex_code VARCHAR(7) DEFAULT NULL, family VARCHAR(100) DEFAULT NULL, tone VARCHAR(100) DEFAULT NULL, temperature VARCHAR(100) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE item_color (id INT AUTO_INCREMENT NOT NULL, is_primary TINYINT DEFAULT 0 NOT NULL, clothing_item_id INT NOT NULL, color_id INT NOT NULL, INDEX IDX_4CF15339AA13B545 (clothing_item_id), INDEX IDX_4CF153397ADA1FB5 (color_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE item_material (id INT AUTO_INCREMENT NOT NULL, percentage DOUBLE PRECISION DEFAULT NULL, clothing_item_id INT NOT NULL, material_id INT NOT NULL, INDEX IDX_10B3BD5EAA13B545 (clothing_item_id), INDEX IDX_10B3BD5EE308AC6F (material_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE material (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, is_warm TINYINT DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pattern (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE style (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sub_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, category_id INT NOT NULL, INDEX IDX_BCE3F79812469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E9F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id)');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E976DA863D FOREIGN KEY (body_zone_id) REFERENCES body_zone (id)');
        $this->addSql('ALTER TABLE clothing_item_season ADD CONSTRAINT FK_1B983599AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_season ADD CONSTRAINT FK_1B9835994EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_style ADD CONSTRAINT FK_41E5B7AFAA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_style ADD CONSTRAINT FK_41E5B7AFBACD6074 FOREIGN KEY (style_id) REFERENCES style (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_pattern ADD CONSTRAINT FK_858EB04CAA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_pattern ADD CONSTRAINT FK_858EB04CF734A20F FOREIGN KEY (pattern_id) REFERENCES pattern (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_color ADD CONSTRAINT FK_4CF15339AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id)');
        $this->addSql('ALTER TABLE item_color ADD CONSTRAINT FK_4CF153397ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (id)');
        $this->addSql('ALTER TABLE item_material ADD CONSTRAINT FK_10B3BD5EAA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id)');
        $this->addSql('ALTER TABLE item_material ADD CONSTRAINT FK_10B3BD5EE308AC6F FOREIGN KEY (material_id) REFERENCES material (id)');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F79812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E9F7BFE87C');
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E976DA863D');
        $this->addSql('ALTER TABLE clothing_item_season DROP FOREIGN KEY FK_1B983599AA13B545');
        $this->addSql('ALTER TABLE clothing_item_season DROP FOREIGN KEY FK_1B9835994EC001D1');
        $this->addSql('ALTER TABLE clothing_item_style DROP FOREIGN KEY FK_41E5B7AFAA13B545');
        $this->addSql('ALTER TABLE clothing_item_style DROP FOREIGN KEY FK_41E5B7AFBACD6074');
        $this->addSql('ALTER TABLE clothing_item_pattern DROP FOREIGN KEY FK_858EB04CAA13B545');
        $this->addSql('ALTER TABLE clothing_item_pattern DROP FOREIGN KEY FK_858EB04CF734A20F');
        $this->addSql('ALTER TABLE item_color DROP FOREIGN KEY FK_4CF15339AA13B545');
        $this->addSql('ALTER TABLE item_color DROP FOREIGN KEY FK_4CF153397ADA1FB5');
        $this->addSql('ALTER TABLE item_material DROP FOREIGN KEY FK_10B3BD5EAA13B545');
        $this->addSql('ALTER TABLE item_material DROP FOREIGN KEY FK_10B3BD5EE308AC6F');
        $this->addSql('ALTER TABLE sub_category DROP FOREIGN KEY FK_BCE3F79812469DE2');
        $this->addSql('DROP TABLE body_zone');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE clothing_item');
        $this->addSql('DROP TABLE clothing_item_season');
        $this->addSql('DROP TABLE clothing_item_style');
        $this->addSql('DROP TABLE clothing_item_pattern');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE item_color');
        $this->addSql('DROP TABLE item_material');
        $this->addSql('DROP TABLE material');
        $this->addSql('DROP TABLE pattern');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE style');
        $this->addSql('DROP TABLE sub_category');
    }
}
