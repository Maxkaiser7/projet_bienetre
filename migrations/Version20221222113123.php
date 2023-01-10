<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222113123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A2BFC902B');
        $this->addSql('DROP INDEX UNIQ_E01FBE6A2BFC902B ON images');
        $this->addSql('ALTER TABLE images CHANGE images_avatar_id internaute_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ACAF41882 FOREIGN KEY (internaute_id) REFERENCES internaute (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6ACAF41882 ON images (internaute_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6ACAF41882');
        $this->addSql('DROP INDEX UNIQ_E01FBE6ACAF41882 ON images');
        $this->addSql('ALTER TABLE images CHANGE internaute_id images_avatar_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A2BFC902B FOREIGN KEY (images_avatar_id) REFERENCES internaute (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6A2BFC902B ON images (images_avatar_id)');
    }
}