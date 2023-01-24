<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120134059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE internautes_prestataires DROP FOREIGN KEY FK_B6559197BE3DB2B7');
        $this->addSql('ALTER TABLE internautes_prestataires DROP FOREIGN KEY FK_B6559197CAF41882');
        $this->addSql('DROP TABLE internautes_prestataires');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE internautes_prestataires (internaute_id INT NOT NULL, prestataire_id INT NOT NULL, INDEX IDX_B6559197CAF41882 (internaute_id), INDEX IDX_B6559197BE3DB2B7 (prestataire_id), PRIMARY KEY(internaute_id, prestataire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE internautes_prestataires ADD CONSTRAINT FK_B6559197BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES prestataire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE internautes_prestataires ADD CONSTRAINT FK_B6559197CAF41882 FOREIGN KEY (internaute_id) REFERENCES internaute (id) ON DELETE CASCADE');
    }
}
