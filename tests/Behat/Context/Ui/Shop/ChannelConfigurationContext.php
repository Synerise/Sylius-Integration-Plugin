<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

class ChannelConfigurationContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $repository,
        private FactoryInterface $factory,
    ) {
    }

    #[Given('the channel has OpenGraph integration enabled')]
    public function theChannelHasOpengraphIntegrationEnabled(): void
    {
        $channelConfiguration = $this->createChannelConfiguration();
        $channelConfiguration->setOpengraphEnabled(true);
        $this->saveChannelConfiguration($channelConfiguration);
    }

    #[Given('the channel has OpenGraph integration disabled')]
    public function theChannelHasOpengraphIntegrationDisabled(): void
    {
        $channelConfiguration = $this->createChannelConfiguration();
        $channelConfiguration->setOpengraphEnabled(false);
        $this->saveChannelConfiguration($channelConfiguration);
    }

    private function createChannelConfiguration(?ChannelInterface $channel = null): ChannelConfigurationInterface
    {
        if (null === $channel && $this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
        }

        $channelConfiguration = $this->factory->createNew();
        $channelConfiguration->setChannel($channel);

        return $channelConfiguration;
    }

    private function saveChannelConfiguration(ChannelConfigurationInterface $channelConfiguration)
    {
        $this->repository->add($channelConfiguration);
        $this->sharedStorage->set('channelConfiguration', $channelConfiguration);
    }
}
