<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727130122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE genre ADD slug VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_835033F8989D9B62 ON genre (slug)');
        $this->addSql('ALTER TABLE game ADD slug VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD deleted_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_232B318C989D9B62 ON game (slug)');
        $this->addSql('ALTER TABLE product CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('DROP INDEX UNIQ_232B318C989D9B62 ON game');
        $this->addSql('ALTER TABLE game DROP slug, DROP created_at, DROP updated_at, DROP deleted_at');
        $this->addSql('DROP INDEX UNIQ_835033F8989D9B62 ON genre');
        $this->addSql('ALTER TABLE genre DROP slug, DROP created_at, DROP updated_at, DROP deleted_at');
        $this->addSql('ALTER TABLE product CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
    }
}
