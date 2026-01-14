<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter ON DELETE SET NULL aux clés étrangères de comment_report
 */
final class Version20251017100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de ON DELETE SET NULL pour les clés étrangères de comment_report vers user';
    }

    public function up(Schema $schema): void
    {
        // Mettre à jour reported_by_id pour le rendre nullable
        $this->addSql('ALTER TABLE comment_report MODIFY reported_by_id INT DEFAULT NULL');
        
        // Créer les contraintes avec ON DELETE SET NULL
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F9671CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F96FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les contraintes avec ON DELETE SET NULL
        $this->addSql('ALTER TABLE comment_report DROP FOREIGN KEY FK_E3C2F9671CE806');
        $this->addSql('ALTER TABLE comment_report DROP FOREIGN KEY FK_E3C2F96FC6B21F1');
        
        // Recréer les anciennes contraintes sans ON DELETE SET NULL
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F9671CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F96FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        
        // Remettre reported_by_id en NOT NULL
        $this->addSql('ALTER TABLE comment_report MODIFY reported_by_id INT NOT NULL');
    }
}
