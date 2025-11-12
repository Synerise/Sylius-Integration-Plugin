<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
//use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepository;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;

#[AsTwigComponent]
class ChannelsConfiguredCheckComponent
{
//    use HookableComponentTrait;

    #[ExposeInTemplate]
    public int $count;

    /**
     * @param ChannelConfigurationRepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     */
    public function __construct(private ChannelConfigurationRepositoryInterface $channelConfigurationRepository)
    {
        $this->count = $this->channelConfigurationRepository->countAll();
    }
}
