<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630124032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE synerise_synchronization_product_attributes (synchronization_config_id INT NOT NULL, attribute_id INT NOT NULL, INDEX IDX_47DB1EFA15E7E326 (synchronization_config_id), INDEX IDX_47DB1EFAB6E62EFA (attribute_id), PRIMARY KEY(synchronization_config_id, attribute_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes ADD CONSTRAINT FK_47DB1EFA15E7E326 FOREIGN KEY (synchronization_config_id) REFERENCES synerise_synchronization_config (id)');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes ADD CONSTRAINT FK_47DB1EFAB6E62EFA FOREIGN KEY (attribute_id) REFERENCES sylius_product_attribute (id)');
        $this->addSql('ALTER TABLE synerise_synchronization_status_customer DROP FOREIGN KEY FK_682765372F5A1AA');
        $this->addSql('ALTER TABLE synerise_synchronization_status_order DROP FOREIGN KEY FK_DF03BCFD72F5A1AA');
        $this->addSql('ALTER TABLE synerise_synchronization_status_product DROP FOREIGN KEY FK_60118C3C72F5A1AA');
        $this->addSql('DROP TABLE synerise_synchronization_status_customer');
        $this->addSql('DROP TABLE synerise_synchronization_status_order');
        $this->addSql('DROP TABLE synerise_synchronization_status_product');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD snrs_params TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE synerise_synchronization DROP INDEX UNIQ_A9B5B81A72F5A1AA, ADD INDEX IDX_A9B5B81A72F5A1AA (channel_id)');
        $this->addSql('ALTER TABLE synerise_synchronization ADD sent INT DEFAULT NULL, ADD since_when DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD until_when DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP current, CHANGE channel_id channel_id INT NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE total total INT DEFAULT NULL');
        $this->addSql('ALTER TABLE synerise_synchronization_config ADD catalog_id INT DEFAULT NULL, ADD product_attribute_value VARCHAR(10) NOT NULL, DROP data_types');
        $this->addSql('ALTER TABLE synerise_workspace ADD permissionsStatus VARCHAR(15) NOT NULL, ADD liveTimeout DOUBLE PRECISION NOT NULL, ADD scheduledTimeout DOUBLE PRECISION NOT NULL, ADD keepAliveEnabled TINYINT(1) NOT NULL, ADD requestLoggingEnabled TINYINT(1) NOT NULL, DROP permissions');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE synerise_synchronization_status_customer (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_682765372F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE synerise_synchronization_status_order (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_DF03BCFD72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE synerise_synchronization_status_product (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_60118C3C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE synerise_synchronization_status_customer ADD CONSTRAINT FK_682765372F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE synerise_synchronization_status_order ADD CONSTRAINT FK_DF03BCFD72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE synerise_synchronization_status_product ADD CONSTRAINT FK_60118C3C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes DROP FOREIGN KEY FK_47DB1EFA15E7E326');
        $this->addSql('ALTER TABLE synerise_synchronization_product_attributes DROP FOREIGN KEY FK_47DB1EFAB6E62EFA');
        $this->addSql('DROP TABLE synerise_synchronization_product_attributes');
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP snrs_params');
        $this->addSql('ALTER TABLE synerise_synchronization DROP INDEX IDX_A9B5B81A72F5A1AA, ADD UNIQUE INDEX UNIQ_A9B5B81A72F5A1AA (channel_id)');
        $this->addSql('ALTER TABLE synerise_synchronization ADD current INT NOT NULL, DROP sent, DROP since_when, DROP until_when, CHANGE channel_id channel_id INT DEFAULT NULL, CHANGE total total INT NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE synerise_synchronization_config ADD data_types JSON DEFAULT NULL, DROP catalog_id, DROP product_attribute_value');
        $this->addSql('ALTER TABLE synerise_workspace ADD permissions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', DROP permissionsStatus, DROP liveTimeout, DROP scheduledTimeout, DROP keepAliveEnabled, DROP requestLoggingEnabled');
    }
}
