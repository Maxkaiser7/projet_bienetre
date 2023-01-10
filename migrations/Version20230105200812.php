<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230105200812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images CHANGE prestataire_id prestataire_id INT DEFAULT NULL');
        //$this->addSql('ALTER TABLE proposer RENAME INDEX idx_21866c15be3db2b7 TO IDX_21866C157359CA6F');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images CHANGE prestataire_id prestataire_id INT DEFAULT NULL');
        //$this->addSql('ALTER TABLE proposer RENAME INDEX idx_21866c157359ca6f TO IDX_21866C15BE3DB2B7');
    }
}
