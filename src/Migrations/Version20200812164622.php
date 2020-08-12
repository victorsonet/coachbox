<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200812164622 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE ordered ordered INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE ordered ordered INT DEFAULT NULL');
        $this->addSql('ALTER TABLE genre CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE ordered ordered INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE game_id game_id INT DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE review CHANGE coach_id coach_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A9600977');
        $this->addSql('DROP INDEX UNIQ_8D93D649A9600977 ON user');
        $this->addSql('ALTER TABLE user DROP coaches_id, CHANGE roles roles JSON NOT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coach CHANGE ordered ordered INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE game CHANGE ordered ordered INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE genre CHANGE ordered ordered INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product CHANGE game_id game_id INT DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE review CHANGE coach_id coach_id INT DEFAULT NULL, CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user ADD coaches_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A9600977 FOREIGN KEY (coaches_id) REFERENCES coach (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A9600977 ON user (coaches_id)');
    }
}
