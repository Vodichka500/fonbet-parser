<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819151314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tournaments (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE sub_matces');
        $this->addSql('ALTER TABLE sub_matches DROP FOREIGN KEY FK_79FE494EC12EE1F6');
        $this->addSql('DROP INDEX IDX_79FE494EC12EE1F6 ON sub_matches');
        $this->addSql('ALTER TABLE sub_matches ADD score1 SMALLINT NOT NULL, ADD score2 SMALLINT NOT NULL, ADD title VARCHAR(255) DEFAULT NULL, DROP match_id_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sub_matces (id INT AUTO_INCREMENT NOT NULL, score1 SMALLINT NOT NULL, score2 SMALLINT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE tournaments');
        $this->addSql('ALTER TABLE sub_matches ADD match_id_id INT NOT NULL, DROP score1, DROP score2, DROP title');
        $this->addSql('ALTER TABLE sub_matches ADD CONSTRAINT FK_79FE494EC12EE1F6 FOREIGN KEY (match_id_id) REFERENCES matches (id)');
        $this->addSql('CREATE INDEX IDX_79FE494EC12EE1F6 ON sub_matches (match_id_id)');
    }
}
