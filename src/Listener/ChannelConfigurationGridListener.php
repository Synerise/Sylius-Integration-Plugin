<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;

class ChannelConfigurationGridListener
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param ChannelConfigurationRepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ChannelConfigurationRepositoryInterface $channelConfigurationRepository
    ) {
    }

    public function displayAddBtn(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();
        $addBtn = $grid->getActionGroup('main')->getAction('create');
        $canAdd = $this->channelRepository->countAll() > $this->channelConfigurationRepository->countAll();
        $addBtn->setEnabled($canAdd);
    }
}
