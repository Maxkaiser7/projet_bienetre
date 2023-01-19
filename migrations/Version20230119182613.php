<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230119182613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favori_internaute DROP FOREIGN KEY FK_7FE97122CAF41882');
        $this->addSql('ALTER TABLE favori_internaute DROP FOREIGN KEY FK_7FE97122FF17033F');
        $this->addSql('ALTER TABLE favori_prestataire DROP FOREIGN KEY FK_2703CC19BE3DB2B7');
        $this->addSql('ALTER TABLE favori_prestataire DROP FOREIGN KEY FK_2703CC19FF17033F');
        $this->addSql('DROP TABLE favori');
        $this->addSql('DROP TABLE favori_internaute');
        $this->addSql('DROP TABLE favori_prestataire');
        $this->addSql('ALTER TABLE images CHANGE categorie_de_services_id categorie_de_services_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favori_internaute (favori_id INT NOT NULL, internaute_id INT NOT NULL, INDEX IDX_7FE97122FF17033F (favori_id), INDEX IDX_7FE97122CAF41882 (internaute_id), PRIMARY KEY(favori_id, internaute_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favori_prestataire (favori_id INT NOT NULL, prestataire_id INT NOT NULL, INDEX IDX_2703CC19FF17033F (favori_id), INDEX IDX_2703CC19BE3DB2B7 (prestataire_id), PRIMARY KEY(favori_id, prestataire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE favori_internaute ADD CONSTRAINT FK_7FE97122CAF41882 FOREIGN KEY (internaute_id) REFERENCES internaute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_internaute ADD CONSTRAINT FK_7FE97122FF17033F FOREIGN KEY (favori_id) REFERENCES favori (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_prestataire ADD CONSTRAINT FK_2703CC19BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_prestataire ADD CONSTRAINT FK_2703CC19FF17033F FOREIGN KEY (favori_id) REFERENCES favori (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images CHANGE categorie_de_services_id categorie_de_services_id INT DEFAULT NULL');
    }
}
