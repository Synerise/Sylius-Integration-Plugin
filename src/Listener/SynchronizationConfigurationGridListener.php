<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;

class SynchronizationConfigurationGridListener
{
    public function __construct(
        private EntityRepository $channelConfigurationRepository,
        private EntityRepository $synchronizationConfigurationRepository
    ) {
    }

    public function displayConfigureBtn(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();
        $configureBtn = $grid->getActionGroup('main')->getAction('create');
        $canConfigure = $this->channelConfigurationRepository->count([]) > $this->synchronizationConfigurationRepository->count([]);
        $configureBtn->setEnabled($canConfigure);
    }
}
