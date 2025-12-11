<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

class SynchronizationConfigurationGridListener
{
    /**
     * @param ChannelConfigurationRepositoryInterface<ChannelConfiguration> $channelConfigurationRepository
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $synchronizationConfigurationRepository
     */
    public function __construct(
        private ChannelConfigurationRepositoryInterface $channelConfigurationRepository,
        private SynchronizationConfigurationRepositoryInterface $synchronizationConfigurationRepository,
    ) {
    }

    public function displayConfigureBtn(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();
        $configureBtn = $grid->getActionGroup('main')->getAction('create');
        $canConfigure = $this->channelConfigurationRepository->countAll() > $this->synchronizationConfigurationRepository->countAll();
        $configureBtn->setEnabled($canConfigure);
    }
}
