<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251020083111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE post ADD title_en VARCHAR(200) DEFAULT NULL, ADD content_en LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(255) NOT NULL, CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE ban_reason ban_reason LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_warning ADD related_report_id INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE issued_by_id issued_by_id INT NOT NULL, CHANGE created_at issued_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_warning ADD CONSTRAINT FK_184382CCE313F8DB FOREIGN KEY (related_report_id) REFERENCES comment_report (id)');
        $this->addSql('CREATE INDEX IDX_184382CCE313F8DB ON user_warning (related_report_id)');
        $this->addSql('ALTER TABLE user_warning RENAME INDEX idx_22160d65a76ed395 TO IDX_184382CCA76ED395');
        $this->addSql('ALTER TABLE user_warning RENAME INDEX idx_22160d6570bc0fc6 TO IDX_184382CC784BB717');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_warning DROP FOREIGN KEY FK_184382CCE313F8DB');
        $this->addSql('DROP INDEX IDX_184382CCE313F8DB ON user_warning');
        $this->addSql('ALTER TABLE user_warning DROP related_report_id, CHANGE user_id user_id INT DEFAULT NULL, CHANGE issued_by_id issued_by_id INT DEFAULT NULL, CHANGE issued_at created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_warning RENAME INDEX idx_184382cc784bb717 TO IDX_22160D6570BC0FC6');
        $this->addSql('ALTER TABLE user_warning RENAME INDEX idx_184382cca76ed395 TO IDX_22160D65A76ED395');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL, CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE ban_reason ban_reason TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE post DROP title_en, DROP content_en');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL');
    }
}
