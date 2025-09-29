<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class ChannelConfigurationContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform saved channel configuration
     */
    public function savedChannelConfiguration(): ChannelConfigurationInterface
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->entityManager->getRepository(ChannelConfiguration::class)->findOneBy(['channel' => $channel]);
        return $configuration;
    }
}
