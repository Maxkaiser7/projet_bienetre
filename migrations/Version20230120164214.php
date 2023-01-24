<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120164214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE internaute_prestataire (favoris INT NOT NULL, prestataire_id INT NOT NULL, INDEX IDX_D906EC3A8933C432 (favoris), INDEX IDX_D906EC3ABE3DB2B7 (prestataire_id), PRIMARY KEY(favoris, prestataire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE internaute_prestataire ADD CONSTRAINT FK_D906EC3A8933C432 FOREIGN KEY (favoris) REFERENCES internaute (id)');
        $this->addSql('ALTER TABLE internaute_prestataire ADD CONSTRAINT FK_D906EC3ABE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE internaute_prestataire DROP FOREIGN KEY FK_D906EC3A8933C432');
        $this->addSql('ALTER TABLE internaute_prestataire DROP FOREIGN KEY FK_D906EC3ABE3DB2B7');
        $this->addSql('DROP TABLE internaute_prestataire');
    }
}
