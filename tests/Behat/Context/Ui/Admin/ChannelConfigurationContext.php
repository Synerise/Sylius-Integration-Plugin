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
     * @Then /^the (saved channel configuration) should exist in repository$/
     */
    public function theChannelConfigurationShouldExist(ChannelConfiguration $channelConfiguration): void
    {
        Assert::notNull($channelConfiguration, 'Saved channel configuration not found');
    }
}
