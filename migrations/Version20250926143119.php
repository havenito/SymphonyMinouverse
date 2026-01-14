<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250926143119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        
        // D'abord, mettre à jour tous les utilisateurs existants avec des valeurs NULL
        $this->addSql('UPDATE user SET is_accepted = 0 WHERE is_accepted IS NULL');
        $this->addSql('UPDATE user SET is_banned = 0 WHERE is_banned IS NULL');
        
        // Ensuite, modifier les colonnes pour avoir des valeurs par défaut
        $this->addSql('ALTER TABLE user CHANGE is_accepted is_accepted TINYINT(1) DEFAULT 0 NOT NULL, CHANGE is_banned is_banned TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_accepted is_accepted TINYINT(1) NOT NULL, CHANGE is_banned is_banned TINYINT(1) NOT NULL');
    }
}
