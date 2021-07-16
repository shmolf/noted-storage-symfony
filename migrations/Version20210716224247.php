<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210716224247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68495E8714');
        $this->addSql('DROP INDEX IDX_B6A2DD68495E8714 ON access_token');
        $this->addSql('ALTER TABLE access_token CHANGE app_token_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B6A2DD68A76ED395 ON access_token (user_id)');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195495E8714');
        $this->addSql('DROP INDEX IDX_C74F2195495E8714 ON refresh_token');
        $this->addSql('ALTER TABLE refresh_token CHANGE app_token_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C74F2195A76ED395 ON refresh_token (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('DROP INDEX IDX_B6A2DD68A76ED395 ON access_token');
        $this->addSql('ALTER TABLE access_token CHANGE user_id app_token_id INT NOT NULL');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68495E8714 FOREIGN KEY (app_token_id) REFERENCES app_token (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B6A2DD68495E8714 ON access_token (app_token_id)');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('DROP INDEX IDX_C74F2195A76ED395 ON refresh_token');
        $this->addSql('ALTER TABLE refresh_token CHANGE user_id app_token_id INT NOT NULL');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195495E8714 FOREIGN KEY (app_token_id) REFERENCES app_token (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C74F2195495E8714 ON refresh_token (app_token_id)');
    }
}
