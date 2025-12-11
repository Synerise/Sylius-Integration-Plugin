<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class ChannelConfigurationContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SharedStorageInterface $sharedStorage,
        private ContainerBagInterface $params,
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

    /**
     * @Transform test tracking code
     */
    public function getTestTrackingCode(): string
    {
        return $this->params->get('synerise.test.tracking_code');
    }
}
