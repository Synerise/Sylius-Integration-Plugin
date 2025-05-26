<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521123643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization_config (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, data_types JSON DEFAULT NULL, UNIQUE INDEX UNIQ_F0CA4B8C72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config ADD CONSTRAINT FK_F0CA4B8C72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization ADD status VARCHAR(255) NOT NULL, ADD type VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', ADD updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', DROP data_types
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization_config DROP FOREIGN KEY FK_F0CA4B8C72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization_config
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization ADD data_types JSON DEFAULT NULL, DROP status, DROP type, DROP created_at, DROP updated_at
        SQL);
    }
}
