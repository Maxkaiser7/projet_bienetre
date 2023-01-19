<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230111152034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C154A81A587');
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C15BE3DB2B7');
        $this->addSql('ALTER TABLE proposer CHANGE prestataire_id prestataire_id INT NOT NULL, CHANGE categorie_de_services_id categorie_de_services_id INT NOT NULL');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C154A81A587 FOREIGN KEY (categorie_de_services_id) REFERENCES categorie_de_services (id)');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C15BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C15BE3DB2B7');
        $this->addSql('ALTER TABLE proposer DROP FOREIGN KEY FK_21866C154A81A587');
        $this->addSql('ALTER TABLE proposer CHANGE prestataire_id prestataire_id INT DEFAULT NULL, CHANGE categorie_de_services_id categorie_de_services_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C15BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE proposer ADD CONSTRAINT FK_21866C154A81A587 FOREIGN KEY (categorie_de_services_id) REFERENCES categorie_de_services (id) ON DELETE CASCADE');
    }
}
