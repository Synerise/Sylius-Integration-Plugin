<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Page\Admin\ChannelConfigurations;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage
{
    public function nextBtn()
    {
        $this->getDocument()->pressButton('Next');
    }

    public function selectChannel(string $channelName)
    {
        $this->getDocument()->selectFieldOption('synerise_integration_channel_configuration_channel', $channelName);
    }

    public function selectWorkspace(string $workspaceName)
    {
        $this->getDocument()->selectFieldOption('synerise_integration_channel_configuration_workspace', $workspaceName);
    }

    public function allowToAutomaticallyAddTrackingCode()
    {
        $this->getDocument()->checkField('synerise_integration_channel_configuration_trackingEnabled');
    }

    public function eventsTrackingTabBtn()
    {
        $this->getDocument()->pressButton('Events tracking');
    }

    public function selectTrackingEvents(array $events)
    {
        foreach ($events as $event) {
            $this->getDocument()->selectFieldOption('synerise_integration_channel_configuration_events-ts-control', $event);
        }
    }

    public function allowToAddTrackingParameters()
    {
        $this->getDocument()->checkField('synerise_integration_channel_configuration_snrsParamsEnabled');
    }

    public function configureBtn()
    {
        $this->getDocument()->pressButton('Configure');
    }
}
