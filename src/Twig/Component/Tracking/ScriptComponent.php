<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Tracking;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

#[AsTwigComponent]
readonly class ScriptComponent
{
    private ChannelConfigurationInterface $channelConfiguration;

    public function __construct(ChannelConfigurationInterface $channelConfiguration)
    {
        $this->channelConfiguration = $channelConfiguration;
    }

    #[ExposeInTemplate('tracking_code')]
    public function trackingCode(): ?string
    {
        return $this->channelConfiguration->getTrackingCode();
    }

    #[ExposeInTemplate('is_tracking_enabled')]
    public function isTrackingEnabled(): bool
    {
        return (bool) $this->channelConfiguration->isTrackingEnabled();
    }
}
