<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

class ChannelConfigurationGridListener
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private EntityRepository $channelConfigurationRepository
    ) {
    }

    public function displayAddBtn(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();
        $addBtn = $grid->getActionGroup('main')->getAction('create');
        $canAdd = $this->channelRepository->countAll() > $this->channelConfigurationRepository->count([]);
        $addBtn->setEnabled($canAdd);
    }
}
