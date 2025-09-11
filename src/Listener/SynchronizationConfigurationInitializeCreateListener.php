<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

class SynchronizationConfigurationInitializeCreateListener
{
    /**
     * @param ChannelConfigurationRepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $synchronizationConfigurationRepository
     */
    public function __construct(
        private ChannelConfigurationRepositoryInterface $channelConfigurationRepository,
        private SynchronizationConfigurationRepositoryInterface $synchronizationConfigurationRepository,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $canCreate = $this->channelConfigurationRepository->countAll() > $this->synchronizationConfigurationRepository->countAll();

        if (!$canCreate) {
            $url = $this->router->generate('synerise_integration_admin_synchronization_configuration_index');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
