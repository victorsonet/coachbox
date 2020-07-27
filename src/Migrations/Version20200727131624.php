<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200727131624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE genre_game (genre_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_98C6E87C4296D31F (genre_id), INDEX IDX_98C6E87CE48FD905 (game_id), PRIMARY KEY(genre_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_coach (game_id INT NOT NULL, coach_id INT NOT NULL, INDEX IDX_D6A1443E48FD905 (game_id), INDEX IDX_D6A14433C105691 (coach_id), PRIMARY KEY(game_id, coach_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genre_game ADD CONSTRAINT FK_98C6E87C4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre_game ADD CONSTRAINT FK_98C6E87CE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_coach ADD CONSTRAINT FK_D6A1443E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_coach ADD CONSTRAINT FK_D6A14433C105691 FOREIGN KEY (coach_id) REFERENCES coach (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE game CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE genre_game');
        $this->addSql('DROP TABLE game_coach');
        $this->addSql('ALTER TABLE coach CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE game CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE genre CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product CHANGE price price INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
    }
}
