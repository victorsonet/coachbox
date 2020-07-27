<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727132358 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD game_id INT DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADE48FD905 ON product (game_id)');
        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE game CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE genre CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE48FD905');
        $this->addSql('DROP INDEX UNIQ_D34A04ADE48FD905 ON product');
        $this->addSql('ALTER TABLE product DROP game_id, CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
    }
}
