<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Hook;

    use Behat\Behat\Hook\Scope\BeforeScenarioScope;
    use Behat\MinkExtension\Context\RawMinkContext;

    final class E2EContext extends RawMinkContext
{
    /** @BeforeScenario */
    public function setE2ECookie(BeforeScenarioScope $scope): void
    {
        $this->getSession()->setCookie('e2e', 1);
    }
}
