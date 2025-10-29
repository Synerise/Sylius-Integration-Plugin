<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Domain;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;

class E2EContext extends RawMinkContext
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    #[Given('the actual api request(s) will be sent')]
    public function testCookie(): void
    {
        $this->sharedStorage->set('e2e', 1);
        $this->getSession()->setCookie('e2e', 1);
    }
}
