<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Step\Then;
use Behat\Step\When;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Webmozart\Assert\Assert;

final class ChannelConfigurationContext extends MinkContext
{
    /**
     * @When I wait for :locator element
     */
    public function IWaitFor(string $locator): void
    {
        $this->getSession()->wait(10000, "document.querySelector('{$locator}') !== null");
    }

    /**
     * @When I click :locator element
     */
    public function iClick(string $locator): void
    {
        $element = $this->getSession()->getPage()->find('css', $locator);
        $element->click();
    }

    /**
     * @Then /^the (saved channel configuration) should exist in repository$/
     */
    public function theChannelConfigurationShouldExist(ChannelConfiguration $channelConfiguration): void
    {
        Assert::notNull($channelConfiguration, 'Saved channel configuration not found');
    }
}
