<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214153853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE synerise_channel_configuration (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, workspace_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_8BF5FA0272F5A1AA (channel_id), INDEX IDX_8BF5FA0282D40A1F (workspace_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE synerise_workspace (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, apiKey BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', apiGuid CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', authenticationMethod VARCHAR(10) NOT NULL, environment VARCHAR(10) NOT NULL, permissions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0272F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('ALTER TABLE synerise_channel_configuration ADD CONSTRAINT FK_8BF5FA0282D40A1F FOREIGN KEY (workspace_id) REFERENCES sylius_channel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0272F5A1AA');
        $this->addSql('ALTER TABLE synerise_channel_configuration DROP FOREIGN KEY FK_8BF5FA0282D40A1F');
        $this->addSql('DROP TABLE synerise_channel_configuration');
        $this->addSql('DROP TABLE synerise_workspace');
    }
}
