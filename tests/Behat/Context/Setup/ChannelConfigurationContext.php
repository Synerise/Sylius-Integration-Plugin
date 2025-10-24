<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\DefaultAzureChannelConfigurationFactory;

class ChannelConfigurationContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private DefaultAzureChannelConfigurationFactory $defaultchannelConfigurationFactory,
        private RepositoryInterface $repository,
        private FactoryInterface $factory,
        private FactoryInterface $workspaceFactory,
        private RepositoryInterface $workspaceRepository,
        private ContainerBagInterface $params,
    ) {
    }

    /**
     * @Given I have a configured channel with workspace
     */
    public function iHaveAChannelWithConfiguration(): void
    {
        $channel = $this->sharedStorage->get('channel');
        $defaultData = $this->defaultchannelConfigurationFactory->create($channel);

        $this->sharedStorage->set('channelConfiguration', $defaultData['channelConfiguration']);
    }

    /**
     * @Given the channel has tracking enabled without tracking code
     * @Given the channel has tracking enabled with tracking code :trackingCode
     */
    public function trackingIsEnabledForTheChannel(?string $trackingCode = null): void
    {
        /** @var ChannelConfigurationInterface $channelConfiguration */
        $channelConfiguration = $this->sharedStorage->get('channelConfiguration');
        $channelConfiguration->setTrackingCode($trackingCode);
        $channelConfiguration->setTrackingEnabled(true);
        $this->saveChannelConfiguration($channelConfiguration);
    }

    /**
     * @Given the channel has tracking disabled
     * @Given the channel has tracking disabled with tracking code :trackingCode
     */
    public function trackingIsDisabledForTheChannel(?string $trackingCode = null): void
    {
        /** @var ChannelConfigurationInterface $channelConfiguration */
        $channelConfiguration = $this->sharedStorage->get('channelConfiguration');
        $channelConfiguration->setTrackingCode($trackingCode);
        $channelConfiguration->setTrackingEnabled(false);
        $this->saveChannelConfiguration($channelConfiguration);
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

    #[Given('the channel is configured with settings:')]
    public function theChannelIsConfiguredWithSettings(TableNode $table): void
    {
        /** @var ChannelConfigurationInterface $channelConfiguration */
        $channelConfiguration = $this->sharedStorage->get('channelConfiguration');

        foreach ($table->getRowsHash() as $key => $value) {
            switch ($key):
                case 'trackingCode':
                    $channelConfiguration->setTrackingCode($value);
                    break;
                case 'customPageVisit':
                    $channelConfiguration->setCustomPageVisit((bool) $value);
                    break;
                case 'cookieDomain':
                    $channelConfiguration->setCookieDomain($value);
                    break;
                case 'virtualPage':
                    $channelConfiguration->setVirtualPage((bool) $value);
                    break;
                case 'events':
                    $channelConfiguration->setEvents(explode(',', $value));
                    break;
                default:
                    throw new \Exception("Undefined key $key");
            endswitch;
        }

        $this->saveChannelConfiguration($channelConfiguration);
    }

    #[Given('the store has a workspace named :workspaceName')]
    public function theStoreHasAWorkspaceNamed(string $workspaceName): void
    {
        $apiKey = $this->params->get('synerise.test.api_key');
        $workspace = $this->workspaceFactory->createNew();
        $workspace->setName($workspaceName);
        $workspace->setApiKey($apiKey);
        $this->workspaceRepository->add($workspace);
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

    private function saveChannelConfiguration(ChannelConfigurationInterface $channelConfiguration): void
    {
        $this->repository->add($channelConfiguration);
        $this->sharedStorage->set('channelConfiguration', $channelConfiguration);
    }
}
