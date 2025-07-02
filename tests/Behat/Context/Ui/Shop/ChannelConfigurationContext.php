<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;

class ChannelConfigurationContext implements Context
{
    #[Given('the channel has OpenGraph integration enabled')]
    public function theChannelHasOpengraphIntegrationEnabled(): void
    {
        throw new PendingException();
    }

}
