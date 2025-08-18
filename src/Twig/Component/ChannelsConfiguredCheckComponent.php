<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepository;

#[AsTwigComponent]
class ChannelsConfiguredCheckComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate]
    public int $count;

    public function __construct(private ChannelConfigurationRepository $channelConfigurationRepository)
    {
        $this->count = $this->channelConfigurationRepository->countAll();
    }
}
