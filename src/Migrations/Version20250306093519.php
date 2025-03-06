<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306093519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0282D40A1F');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD tracking_code VARCHAR(255) DEFAULT NULL, ADD tracking_enabled TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0282D40A1F FOREIGN KEY (workspace_id) REFERENCES synerise_workspace (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0282D40A1F');
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP tracking_code, DROP tracking_enabled');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0282D40A1F FOREIGN KEY (workspace_id) REFERENCES sylius_channel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
