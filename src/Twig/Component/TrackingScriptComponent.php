<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

#[AsTwigComponent]
readonly class TrackingScriptComponent
{
    private ChannelConfigurationInterface $channelConfiguration;

    public function __construct(ChannelConfigurationInterface $channelConfiguration)
    {
        $this->channelConfiguration = $channelConfiguration;
    }

    #[ExposeInTemplate('is_enabled')]
    public function isEnabled(): bool
    {
        return (bool) $this->channelConfiguration->isTrackingEnabled();
    }

    #[ExposeInTemplate('host')]
    public function host(): ?string
    {
        return $this->channelConfiguration->getWorkspace()?->getEnvironment()?->getTrackerHost();
    }

    #[ExposeInTemplate('options')]
    public function options(): array
    {
        $options = [];

        if ($this->channelConfiguration->getTrackingCode()) {
            $options['trackerKey'] = $this->channelConfiguration->getTrackingCode();
        }

        if ($cookieDomain = $this->channelConfiguration->getCookieDomain()) {
            $options['domain'] = '.' . $cookieDomain;
        }

        if ($this->channelConfiguration->isCustomPageVisit()) {
            $options['customPageVisit'] = true;
        }

        if ($this->channelConfiguration->isVirtualPage()) {
            $options['dynamicContent'] = ['virtualPage' => true];
        }

        return $options;
    }
}
