<?php

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

class ChannelConfigurationDeleteListener
{
    public function __construct(
        private SynchronizationConfigurationRepositoryInterface $synchronizationConfigurationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        $configurationForDeletion = $this->synchronizationConfigurationRepository
            ->findOneBy(['channel' => $event->getSubject()->getChannel()?->getId()]);

        if ($configurationForDeletion) {
            $this->entityManager->remove($configurationForDeletion);
            $this->entityManager->flush();
        }
    }
}
