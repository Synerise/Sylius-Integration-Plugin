<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\MinkExtension\Context\MinkContext;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfiguration;
use Webmozart\Assert\Assert;

final class SynchronizationConfigurationContext extends MinkContext
{
    /**
     * @Then /^the (saved synchronization configuration) should exist in repository$/
     */
    public function theChannelConfigurationShouldExist(SynchronizationConfiguration $synchronizationConfiguration): void
    {
        Assert::notNull($synchronizationConfiguration, 'Saved synchronization configuration not found');
    }

    /**
     * @Then /^the "(?P<element>[^"]*)" element should contain (catalog name)$/
     */
    public function assertElementContainsNewCatalogName($element, $name)
    {
        $this->assertElementContains($element, $name);
    }
}
