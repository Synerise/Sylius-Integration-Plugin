<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306141705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD cookie_domain VARCHAR(255) DEFAULT NULL, ADD custom_page_visit TINYINT(1) NOT NULL, ADD virtual_page TINYINT(1) NOT NULL, CHANGE tracking_enabled tracking_enabled TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP cookie_domain, DROP custom_page_visit, DROP virtual_page, CHANGE tracking_enabled tracking_enabled TINYINT(1) DEFAULT NULL');
    }
}
