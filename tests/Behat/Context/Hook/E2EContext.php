<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Hook;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Sylius\Behat\Service\SharedStorageInterface;

final class E2EContext extends RawMinkContext
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /** @BeforeScenario */
    public function setE2ECookie(BeforeScenarioScope $scope): void
    {
        $this->sharedStorage->set('e2e', 1);
        $this->getSession()->setCookie('e2e', 1);
    }
}
