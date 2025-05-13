<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513151830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization_status_customer (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_682765372F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization_status_order (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_DF03BCFD72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization_status_product (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_60118C3C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_customer ADD CONSTRAINT FK_682765372F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_order ADD CONSTRAINT FK_DF03BCFD72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_product ADD CONSTRAINT FK_60118C3C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_customer DROP FOREIGN KEY FK_682765372F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_order DROP FOREIGN KEY FK_DF03BCFD72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_status_product DROP FOREIGN KEY FK_60118C3C72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization_status_customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization_status_order
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization_status_product
        SQL);
    }
}
