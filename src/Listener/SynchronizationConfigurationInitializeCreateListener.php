<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class SynchronizationConfigurationInitializeCreateListener
{
    public function __construct(
        private EntityRepository $channelConfigurationRepository,
        private EntityRepository $synchronizationConfigurationRepository,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $canCreate = $this->channelConfigurationRepository->count([]) > $this->synchronizationConfigurationRepository->count([]);

        if (!$canCreate) {
            $url = $this->router->generate('synerise_integration_admin_synchronization_configuration_index');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
