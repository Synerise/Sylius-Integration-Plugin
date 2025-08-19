<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630111159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE synerise_channel_configuration (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, workspace_id INT DEFAULT NULL, tracking_code VARCHAR(255) DEFAULT NULL, tracking_enabled TINYINT(1) NOT NULL, cookie_domain VARCHAR(255) DEFAULT NULL, custom_page_visit TINYINT(1) NOT NULL, virtual_page TINYINT(1) NOT NULL, opengraph_enabled TINYINT(1) NOT NULL, events JSON DEFAULT NULL, queue_events JSON DEFAULT NULL, snrs_params TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8BF5FA0272F5A1AA (channel_id), INDEX IDX_8BF5FA0282D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synerise_synchronization (id INT AUTO_INCREMENT NOT NULL, channel_id INT NOT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, configuration_snapshot VARCHAR(255) NOT NULL, total INT DEFAULT NULL, sent INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, since_when DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', until_when DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A9B5B81A72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synerise_synchronization_config (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, catalog_id INT DEFAULT NULL, product_attribute_value VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_F0CA4B8C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synerise_synchronization_product_attributes (synchronization_config_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_47DB1EFA15E7E326 (synchronization_config_id), INDEX IDX_47DB1EFAB6E62EFA (attribute_id), PRIMARY KEY(synchronization_config_id, attribute_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synerise_workspace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, apiKey BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', apiGuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', authenticationMethod VARCHAR(10) NOT NULL, environment VARCHAR(10) NOT NULL, permissionsStatus VARCHAR(15) NOT NULL, liveTimeout DOUBLE PRECISION NOT NULL, scheduledTimeout DOUBLE PRECISION NOT NULL, keepAliveEnabled TINYINT(1) NOT NULL, requestLoggingEnabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0272F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0282D40A1F FOREIGN KEY (workspace_id) REFERENCES synerise_workspace (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE synerise_synchronization ADD CONSTRAINT FK_A9B5B81A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE synerise_synchronization_config ADD CONSTRAINT FK_F0CA4B8C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes ADD CONSTRAINT FK_47DB1EFA15E7E326 FOREIGN KEY (synchronization_config_id) REFERENCES synerise_synchronization_config (id)');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes ADD CONSTRAINT FK_47DB1EFAB6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0272F5A1AA');
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0282D40A1F');
        $this->addSql('ALTER TABLE synerise_synchronization DROP FOREIGN KEY FK_A9B5B81A72F5A1AA');
        $this->addSql('ALTER TABLE synerise_synchronization_config DROP FOREIGN KEY FK_F0CA4B8C72F5A1AA');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes DROP FOREIGN KEY FK_47DB1EFA15E7E326');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes DROP FOREIGN KEY FK_47DB1EFAB6E62EFA');
        $this->addSql('DROP TABLE synerise_channel_configuration');
        $this->addSql('DROP TABLE synerise_synchronization');
        $this->addSql('DROP TABLE synerise_synchronization_config');
        $this->addSql('DROP TABLE synerise_synchronization_product_attributes');
        $this->addSql('DROP TABLE synerise_workspace');
    }
}
