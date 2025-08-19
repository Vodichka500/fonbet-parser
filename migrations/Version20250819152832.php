<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819152832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_matches DROP FOREIGN KEY FK_79FE494EC12EE1F6');
        $this->addSql('DROP INDEX IDX_79FE494EC12EE1F6 ON sub_matches');
        $this->addSql('ALTER TABLE sub_matches CHANGE match_id_id match_id INT NOT NULL');
        $this->addSql('ALTER TABLE sub_matches ADD CONSTRAINT FK_79FE494E2ABEACD6 FOREIGN KEY (match_id) REFERENCES matches (id)');
        $this->addSql('CREATE INDEX IDX_79FE494E2ABEACD6 ON sub_matches (match_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sub_matches DROP FOREIGN KEY FK_79FE494E2ABEACD6');
        $this->addSql('DROP INDEX IDX_79FE494E2ABEACD6 ON sub_matches');
        $this->addSql('ALTER TABLE sub_matches CHANGE match_id match_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE sub_matches ADD CONSTRAINT FK_79FE494EC12EE1F6 FOREIGN KEY (match_id_id) REFERENCES matches (id)');
        $this->addSql('CREATE INDEX IDX_79FE494EC12EE1F6 ON sub_matches (match_id_id)');
    }
}
