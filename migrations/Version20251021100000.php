<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251021100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add two-factor authentication fields to user table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD two_factor_enabled TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD two_factor_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD backup_codes JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP two_factor_enabled');
        $this->addSql('ALTER TABLE user DROP two_factor_secret');
        $this->addSql('ALTER TABLE user DROP backup_codes');
    }
}
