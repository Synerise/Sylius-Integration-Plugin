<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;

class SynchronizationConfigurationContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform saved synchronization configuration
     */
    public function savedSynchronizationConfiguration(): SynchronizationConfigurationInterface
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->entityManager->getRepository(SynchronizationConfiguration::class)->findOneBy(['channel' => $channel]);
        return $configuration;
    }
}
