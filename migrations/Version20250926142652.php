<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250926142652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // D'abord, mettre à jour tous les utilisateurs existants avec warning_count NULL ou non défini
        $this->addSql('UPDATE user SET warning_count = 0 WHERE warning_count IS NULL');
        
        // Ensuite, modifier la colonne pour avoir une valeur par défaut
        $this->addSql('ALTER TABLE user CHANGE warning_count warning_count INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE warning_count warning_count INT NOT NULL');
    }
}
