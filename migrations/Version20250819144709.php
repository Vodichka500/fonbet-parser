<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819144709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE matches (id INT AUTO_INCREMENT NOT NULL, source_id VARCHAR(255) NOT NULL, discipline VARCHAR(255) NOT NULL, tournire VARCHAR(255) DEFAULT NULL, match_format VARCHAR(255) NOT NULL, score1 SMALLINT DEFAULT NULL, score2 SMALLINT DEFAULT NULL, team1 VARCHAR(255) NOT NULL, team2 VARCHAR(255) NOT NULL, winning_team VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, submatches_number SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE matches');
    }
}
