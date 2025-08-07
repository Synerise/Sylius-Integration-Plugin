<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;

class ChannelConfigurationInitializeCreateListener
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     * @param ChannelConfigurationRepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ChannelConfigurationRepositoryInterface $channelConfigurationRepository,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $canCreate = $this->channelRepository->countAll() > $this->channelConfigurationRepository->countAll();

        if (!$canCreate) {
            $url = $this->router->generate('synerise_integration_admin_channel_configuration_index');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
