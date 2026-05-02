<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416151533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create tables for weather conditions and clothing item weather conditions and update schema';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clothing_item_weather_condition (clothing_item_id INT NOT NULL, weather_condition_id INT NOT NULL, INDEX IDX_B8317F59AA13B545 (clothing_item_id), INDEX IDX_B8317F5986C2CF78 (weather_condition_id), PRIMARY KEY (clothing_item_id, weather_condition_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE weather_condition (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE clothing_item_weather_condition ADD CONSTRAINT FK_B8317F59AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item_weather_condition ADD CONSTRAINT FK_B8317F5986C2CF78 FOREIGN KEY (weather_condition_id) REFERENCES weather_condition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY `FK_CFE0A4E976DA863D`');
        $this->addSql('DROP INDEX IDX_CFE0A4E976DA863D ON clothing_item');
        $this->addSql('DROP TABLE body_zone');
        $this->addSql('ALTER TABLE clothing_item ADD body_zone VARCHAR(255) NOT NULL, DROP body_zone_id');
        $this->addSql('ALTER TABLE color ADD saturation VARCHAR(255) NOT NULL, CHANGE family family VARCHAR(255) NOT NULL, CHANGE tone tone VARCHAR(255) NOT NULL, CHANGE temperature temperature VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pattern ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE season ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE style ADD type VARCHAR(255) NOT NULL, DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE body_zone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE clothing_item_weather_condition DROP FOREIGN KEY FK_B8317F59AA13B545');
        $this->addSql('ALTER TABLE clothing_item_weather_condition DROP FOREIGN KEY FK_B8317F5986C2CF78');
        $this->addSql('DROP TABLE clothing_item_weather_condition');
        $this->addSql('DROP TABLE weather_condition');
        $this->addSql('ALTER TABLE clothing_item ADD body_zone_id INT DEFAULT NULL, DROP body_zone');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT `FK_CFE0A4E976DA863D` FOREIGN KEY (body_zone_id) REFERENCES body_zone (id)');
        $this->addSql('CREATE INDEX IDX_CFE0A4E976DA863D ON clothing_item (body_zone_id)');
        $this->addSql('ALTER TABLE color DROP saturation, CHANGE family family VARCHAR(100) DEFAULT NULL, CHANGE tone tone VARCHAR(100) DEFAULT NULL, CHANGE temperature temperature VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE pattern DROP type');
        $this->addSql('ALTER TABLE season DROP type');
        $this->addSql('ALTER TABLE style ADD description LONGTEXT DEFAULT NULL, DROP type');
    }
}
