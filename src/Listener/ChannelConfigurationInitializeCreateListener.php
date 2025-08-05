<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class ChannelConfigurationInitializeCreateListener
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private EntityRepository $channelConfigurationRepository,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $canCreate = $this->channelRepository->countAll() > $this->channelConfigurationRepository->count([]);

        if (!$canCreate) {
            $url = $this->router->generate('synerise_integration_admin_channel_configuration_index');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
