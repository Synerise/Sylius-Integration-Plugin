<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625100251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration ADD snrs_params TINYINT(1) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config DROP data_types
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration DROP snrs_params
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config ADD data_types JSON DEFAULT NULL
        SQL);
    }
}
