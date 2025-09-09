<?php

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

class ChannelConfigurationDeleteListener
{
    /**
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $synchronizationConfigurationRepository
     */
    public function __construct(
        private SynchronizationConfigurationRepositoryInterface $synchronizationConfigurationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /** @var ChannelConfigurationInterface $channelConfiguration */
        $channelConfiguration = $event->getSubject();
        $channelId = $channelConfiguration->getChannel()?->getId();

        $configurationForDeletion = $channelId ?: $this->synchronizationConfigurationRepository
            ->findOneBy(['channel' => $channelId]);

        if ($configurationForDeletion) {
            $this->entityManager->remove($configurationForDeletion);
            $this->entityManager->flush();
        }
    }
}
