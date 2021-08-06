<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210703190625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3DDA5752710541FE ON markdown_note');
        $this->addSql('ALTER TABLE markdown_note DROP client_uuid, CHANGE content content MEDIUMTEXT DEFAULT NULL, CHANGE note_uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DDA5752D17F50A6 ON markdown_note (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_3DDA5752D17F50A6 ON markdown_note');
        $this->addSql('ALTER TABLE markdown_note ADD client_uuid CHAR(36) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\', CHANGE content content MEDIUMTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE uuid note_uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:guid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3DDA5752710541FE ON markdown_note (note_uuid)');
    }
}
