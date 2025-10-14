<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Hook;

    use Behat\Behat\Context\Context;
    use Behat\Behat\Hook\Scope\BeforeScenarioScope;
    use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\LogParser;

    final class LogContext implements Context
{
    public function __construct(private LogParser $logParser)
    {
    }

    /** @BeforeScenario */
    public function purgeLogs(BeforeScenarioScope $scope): void
    {
        $this->logParser->purgeLogs();
    }
}
