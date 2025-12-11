<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Doctrine\DBAL\Connection;

class MessengerContext implements Context
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /** @BeforeScenario */
    public function clearMessengerQueue(BeforeScenarioScope $scope): void
    {
        $this->connection->executeStatement('DELETE FROM messenger_messages');
    }
}
