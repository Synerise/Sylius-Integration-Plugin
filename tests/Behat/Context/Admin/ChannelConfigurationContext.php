<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Admin;

use Behat\Step\Then;
use Behat\Step\When;
use Behat\Step\Given;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;

class ChannelConfigurationContext implements Context
{
    #[Given('the store is available in :arg1')]
    public function theStoreIsAvailableIn($arg1): void
    {
        throw new PendingException();
    }

    #[Given('I am logged in as an administrator')]
    public function iAmLoggedInAsAnAdministrator(): void
    {
        throw new PendingException();
    }

    #[When('I want to create channel configuration')]
    public function iWantToCreateChannelConfiguration(): void
    {
        throw new PendingException();
    }

    #[When('I select channel')]
    public function iSelectChannel(): void
    {
        throw new PendingException();
    }

    #[When('I select workspace')]
    public function iSelectWorkspace(): void
    {
        throw new PendingException();
    }

    #[When('I click :arg1 button')]
    public function iClickButton($arg1): void
    {
        throw new PendingException();
    }

    #[When('I set :arg1 to enabled')]
    public function iSetToEnabled($arg1): void
    {
        throw new PendingException();
    }

    #[When('I click :arg1 button from side nav')]
    public function iClickButtonFromSideNav($arg1): void
    {
        throw new PendingException();
    }

    #[When('I select :arg1 and :arg2 in a Tracking events')]
    public function iSelectAndInATrackingEvents($arg1, $arg2): void
    {
        throw new PendingException();
    }

    #[Then('I should be notified that it has been successfully configured')]
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyConfigured(): void
    {
        throw new PendingException();
    }

    #[Then('I should see channel and workspace names in the first tab')]
    public function iShouldSeeChannelAndWorkspaceNamesInTheFirstTab(): void
    {
        throw new PendingException();
    }

    #[Then('I should see :arg1 in a :arg2 row at second tab')]
    public function iShouldSeeInARowAtSecondTab($arg1, $arg2): void
    {
        throw new PendingException();
    }

    #[Then('I should see domain name in a :arg1 row at second tab')]
    public function iShouldSeeDomainNameInARowAtSecondTab($arg1): void
    {
        throw new PendingException();
    }

    #[Then('I should see selected tracking events :arg1 and :arg2 in a :arg3 row at third tab')]
    public function iShouldSeeSelectedTrackingEventsAndInARowAtThirdTab($arg1, $arg2, $arg3): void
    {
        throw new PendingException();
    }

    #[Then('I should see :arg1 in a :arg2 row at third tab')]
    public function iShouldSeeInARowAtThirdTab($arg1, $arg2): void
    {
        throw new PendingException();
    }
}
