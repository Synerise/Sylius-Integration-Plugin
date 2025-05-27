<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527124734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_channel_configuration (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, workspace_id INT DEFAULT NULL, tracking_code VARCHAR(255) DEFAULT NULL, tracking_enabled TINYINT(1) NOT NULL, cookie_domain VARCHAR(255) DEFAULT NULL, custom_page_visit TINYINT(1) NOT NULL, virtual_page TINYINT(1) NOT NULL, opengraph_enabled TINYINT(1) NOT NULL, events JSON DEFAULT NULL, queue_events JSON DEFAULT NULL, UNIQUE INDEX UNIQ_8BF5FA0272F5A1AA (channel_id), INDEX IDX_8BF5FA0282D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, configuration_snapshot VARCHAR(255) NOT NULL, total INT DEFAULT NULL, sent INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_A9B5B81A72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization_config (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, data_types JSON DEFAULT NULL, product_attributes JSON DEFAULT NULL, catalog_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_F0CA4B8C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_workspace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, apiKey BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', apiGuid CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)', authenticationMethod VARCHAR(10) NOT NULL, environment VARCHAR(10) NOT NULL, permissions LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0272F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0282D40A1F FOREIGN KEY (workspace_id) REFERENCES synerise_workspace (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization ADD CONSTRAINT FK_A9B5B81A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config ADD CONSTRAINT FK_F0CA4B8C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0272F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0282D40A1F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization DROP FOREIGN KEY FK_A9B5B81A72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config DROP FOREIGN KEY FK_F0CA4B8C72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_channel_configuration
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization_config
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_workspace
        SQL);
    }
}
