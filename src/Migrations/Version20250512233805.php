<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512233805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE synerise_synchronization (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, data_types JSON DEFAULT NULL, UNIQUE INDEX UNIQ_A9B5B81A72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization ADD CONSTRAINT FK_A9B5B81A72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE synerise_synchronization DROP FOREIGN KEY FK_A9B5B81A72F5A1AA
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE synerise_synchronization
        SQL);
    }
}
