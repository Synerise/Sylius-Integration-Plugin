<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;

class SynchronizationConfigurationContext extends RawMinkContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage
    ) {
    }

    /**
     * @Given /^there is a synerise catalog for (this channel)$/
     */
    public function theCatalogExist($channel): void
    {
        $this->sharedStorage->set('channel', $channel);
        $this->getSession()->setCookie('channelId', $channel->getId());
    }
}
