<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250926131813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_suggestion (id INT AUTO_INCREMENT NOT NULL, suggested_by_id INT NOT NULL, reviewed_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, suggested_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, reviewed_at DATETIME DEFAULT NULL, INDEX IDX_FB9EA36566290AB1 (suggested_by_id), INDEX IDX_FB9EA365FC6B21F1 (reviewed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment_report (id INT AUTO_INCREMENT NOT NULL, comment_id INT NOT NULL, reported_by_id INT NOT NULL, reviewed_by_id INT DEFAULT NULL, report_category VARCHAR(255) NOT NULL, reason LONGTEXT DEFAULT NULL, reported_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, reviewed_at DATETIME DEFAULT NULL, INDEX IDX_E3C2F96F8697D13 (comment_id), INDEX IDX_E3C2F9671CE806 (reported_by_id), INDEX IDX_E3C2F96FC6B21F1 (reviewed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_warning (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, issued_by_id INT NOT NULL, related_report_id INT DEFAULT NULL, reason LONGTEXT NOT NULL, issued_at DATETIME NOT NULL, INDEX IDX_184382CCA76ED395 (user_id), INDEX IDX_184382CC784BB717 (issued_by_id), INDEX IDX_184382CCE313F8DB (related_report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_suggestion ADD CONSTRAINT FK_FB9EA36566290AB1 FOREIGN KEY (suggested_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category_suggestion ADD CONSTRAINT FK_FB9EA365FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F96F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F9671CE806 FOREIGN KEY (reported_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment_report ADD CONSTRAINT FK_E3C2F96FC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_warning ADD CONSTRAINT FK_184382CCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_warning ADD CONSTRAINT FK_184382CC784BB717 FOREIGN KEY (issued_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_warning ADD CONSTRAINT FK_184382CCE313F8DB FOREIGN KEY (related_report_id) REFERENCES comment_report (id)');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0A76ED395');
        $this->addSql('ALTER TABLE reply DROP FOREIGN KEY FK_FDA8C6E0F8697D13');
        $this->addSql('DROP TABLE reply');
        $this->addSql('ALTER TABLE user ADD warning_count INT NOT NULL, ADD is_banned TINYINT(1) NOT NULL, ADD banned_at DATETIME DEFAULT NULL, ADD ban_reason LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reply (id INT AUTO_INCREMENT NOT NULL, comment_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, created_at DATETIME NOT NULL, INDEX IDX_FDA8C6E0F8697D13 (comment_id), INDEX IDX_FDA8C6E0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E0F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE category_suggestion DROP FOREIGN KEY FK_FB9EA36566290AB1');
        $this->addSql('ALTER TABLE category_suggestion DROP FOREIGN KEY FK_FB9EA365FC6B21F1');
        $this->addSql('ALTER TABLE comment_report DROP FOREIGN KEY FK_E3C2F96F8697D13');
        $this->addSql('ALTER TABLE comment_report DROP FOREIGN KEY FK_E3C2F9671CE806');
        $this->addSql('ALTER TABLE comment_report DROP FOREIGN KEY FK_E3C2F96FC6B21F1');
        $this->addSql('ALTER TABLE user_warning DROP FOREIGN KEY FK_184382CCA76ED395');
        $this->addSql('ALTER TABLE user_warning DROP FOREIGN KEY FK_184382CC784BB717');
        $this->addSql('ALTER TABLE user_warning DROP FOREIGN KEY FK_184382CCE313F8DB');
        $this->addSql('DROP TABLE category_suggestion');
        $this->addSql('DROP TABLE comment_report');
        $this->addSql('DROP TABLE user_warning');
        $this->addSql('ALTER TABLE user DROP warning_count, DROP is_banned, DROP banned_at, DROP ban_reason');
    }
}
