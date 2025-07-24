<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;

final readonly class SynchronizationPreCreateListener
{
    public function __construct(
        private RequestStack $requestStack,
        private SynchronizationConfigurationRepository $repository,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }
        $configurationId = $request->get('configurationId');
        $synchronizationConfiguration = $this->repository->find($configurationId);

        $synchronization->setStatus(SynchronizationStatus::Created);
        $synchronization->setChannel($synchronizationConfiguration->getChannel());
        $synchronization->setConfigurationSnapshot(json_encode($synchronizationConfiguration) ?: null);
        $synchronization->setSent(0);
    }
}
