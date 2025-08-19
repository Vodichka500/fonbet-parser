<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819152345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches ADD tournament_id INT DEFAULT NULL, ADD team1_id INT DEFAULT NULL, ADD team2_id INT DEFAULT NULL, DROP tournire, DROP team1, DROP team2');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BA33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournaments (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAE72BCFA4 FOREIGN KEY (team1_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE matches ADD CONSTRAINT FK_62615BAF59E604A FOREIGN KEY (team2_id) REFERENCES teams (id)');
        $this->addSql('CREATE INDEX IDX_62615BA33D1A3E7 ON matches (tournament_id)');
        $this->addSql('CREATE INDEX IDX_62615BAE72BCFA4 ON matches (team1_id)');
        $this->addSql('CREATE INDEX IDX_62615BAF59E604A ON matches (team2_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BA33D1A3E7');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAE72BCFA4');
        $this->addSql('ALTER TABLE matches DROP FOREIGN KEY FK_62615BAF59E604A');
        $this->addSql('DROP INDEX IDX_62615BA33D1A3E7 ON matches');
        $this->addSql('DROP INDEX IDX_62615BAE72BCFA4 ON matches');
        $this->addSql('DROP INDEX IDX_62615BAF59E604A ON matches');
        $this->addSql('ALTER TABLE matches ADD tournire VARCHAR(255) DEFAULT NULL, ADD team1 VARCHAR(255) NOT NULL, ADD team2 VARCHAR(255) NOT NULL, DROP tournament_id, DROP team1_id, DROP team2_id');
    }
}
