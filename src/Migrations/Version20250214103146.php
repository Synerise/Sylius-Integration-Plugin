<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214103146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE synerise_workspace (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, apiKey BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', apiGuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', authenticationMethod VARCHAR(10) NOT NULL, environment VARCHAR(10) NOT NULL, permissions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_FA3F4BDF72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE synerise_workspace ADD CONSTRAINT FK_FA3F4BDF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_workspace DROP FOREIGN KEY FK_FA3F4BDF72F5A1AA');
        $this->addSql('DROP TABLE synerise_workspace');
    }
}
