<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222110938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favori_prestataire (favori_id INT NOT NULL, prestataire_id INT NOT NULL, INDEX IDX_2703CC19FF17033F (favori_id), INDEX IDX_2703CC19BE3DB2B7 (prestataire_id), PRIMARY KEY(favori_id, prestataire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favori_prestataire ADD CONSTRAINT FK_2703CC19FF17033F FOREIGN KEY (favori_id) REFERENCES favori (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favori_prestataire ADD CONSTRAINT FK_2703CC19BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favori_prestataire DROP FOREIGN KEY FK_2703CC19FF17033F');
        $this->addSql('ALTER TABLE favori_prestataire DROP FOREIGN KEY FK_2703CC19BE3DB2B7');
        $this->addSql('DROP TABLE favori_prestataire');
    }
}
