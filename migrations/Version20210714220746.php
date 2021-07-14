<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714220746 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, app_token_id INT NOT NULL, creation_date DATETIME NOT NULL, expiration_date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_B6A2DD68495E8714 (app_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, app_token_id INT NOT NULL, creation_date DATETIME NOT NULL, expiration_date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_C74F2195495E8714 (app_token_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68495E8714 FOREIGN KEY (app_token_id) REFERENCES app_token (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195495E8714 FOREIGN KEY (app_token_id) REFERENCES app_token (id)');
        $this->addSql('ALTER TABLE markdown_note CHANGE content content MEDIUMTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('ALTER TABLE markdown_note CHANGE content content MEDIUMTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
