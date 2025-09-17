<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Step\Then;
use Behat\Step\When;
use Behat\Step\Given;
use Behat\MinkExtension\Context\MinkContext;
use Webmozart\Assert\Assert;

final class ChannelConfigurationContext extends MinkContext
{
    #[Given('I am on the channel configuration creation page')]
    public function iAmOnTheChannelConfigurationCreationPage(): void
    {
        $this->visit('/admin/synerise/configuration/new');
    }

    #[When('I select :value from :field')]
    public function iSelect(string $value, string $field): void
    {
        $this->getSession()->getPage()->selectFieldOption($field, $value);
    }

    #[When('I click :btn button')]
    public function iClickButton(string $btn): void
    {
        $this->getSession()->getPage()->pressButton($btn);
    }

    #[When('I set :field to enabled')]
    public function iEnable(string $field): void
    {
        $this->getSession()->getPage()->checkField($field);
    }

    #[When('I fill :value in :field')]
    public function iFillIn(string $value, string $field): void
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    #[When('I select :event1 and :event2 in a :field')]
    public function iSelectEvents(string $event1, string $event2, string $field): void
    {
        $this->getSession()->getPage()->selectFieldOption($field, $event1, true);
        $this->getSession()->getPage()->selectFieldOption($field, $event2, true);
    }

    #[Then('I should be notified that it has been successfully configured')]
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyConfigured(): void
    {
        $notification = $this->getSession()->getPage()->find('css', '.alert[data-test-sylius-flash-message-type="success"]');
        Assert::notNull($notification, 'Success notification was not found');
        Assert::contains($notification->getText(), "Channel configuration has been successfully created.");
    }

    #[Then('I should see :channelName channel and :workspaceName workspace names in the :tab tab')]
    public function iShouldSeeChannelAndWorkspaceNamesInTheFirstTab(string $channelName, string $workspaceName, string $tab): void
    {
        $tabEl = $this->getSession()->getPage()->find('css', ".page-body .card:nth-child({$tab})");
        Assert::contains($tabEl->getText(), $channelName);
        Assert::contains($tabEl->getText(), $workspaceName);
    }

    #[Then('I should see :value in a :row row at :tab tab')]
    public function iShouldSeeXInAYRowAtZTab(string $value, string $row, string $tab): void
    {
        $tabEl = $this->getSession()->getPage()->find('css', ".page-body .card:nth-child({$tab})");
        $rowEl = $tabEl->find('css', "tr:nth-child({$row})");
        $valueEl = $rowEl->find('css', "td:last-child");
        Assert::contains($valueEl->getText(), $value);
    }

    #[Then('I should see selected tracking events :event1 and :event2 in a :row row at :tab tab')]
    public function iShouldSeeSelectedTrackingEventsAndInARowAtThirdTab(string $event1, string $event2, string $row, string $tab): void
    {
        $tabEl = $this->getSession()->getPage()->find('css', ".page-body .card:nth-child({$tab})");
        $rowEl = $tabEl->find('css', "tr:nth-child({$row})");
        $valueEl = $rowEl->find('css', "td:last-child");
        Assert::contains($valueEl->getText(), $event1);
        Assert::contains($valueEl->getText(), $event2);
    }
}
