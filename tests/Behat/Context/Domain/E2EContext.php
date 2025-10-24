<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Domain;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;

class E2EContext extends RawMinkContext
{
    #[Given('the actual api request(s) will be sent')]
    public function testCookie(): void
    {
        $this->getSession()->setCookie('e2e', 1);
    }
}
