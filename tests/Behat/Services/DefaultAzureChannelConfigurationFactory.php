<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;

class DefaultAzureChannelConfigurationFactory
{
    public const DEFAULT_WORKSPACE_NAME = 'Azure Workspace';
    public const DEFAULT_API_KEY = 'cd27ee0f-93e9-4bac-a927-c383d15de14f';

    /**
     * @param FactoryInterface<ChannelConfigurationInterface> $channelConfigurationFactory
     * @param FactoryInterface<WorkspaceInterface> $workspaceFactory
     * @param RepositoryInterface<ChannelConfigurationInterface> $channelConfigurationRepository
     * @param RepositoryInterface<WorkspaceInterface> $workspaceRepository
     */
    public function __construct(
        private FactoryInterface $channelConfigurationFactory,
        private FactoryInterface $workspaceFactory,
        private RepositoryInterface $channelConfigurationRepository,
        private RepositoryInterface $workspaceRepository,
    ) {
    }

    public function create(ChannelInterface $channel, ?WorkspaceInterface $workspace = null): array
    {
        if ($workspace === null) {
            $workspace = $this->createWorkspace();
            $this->workspaceRepository->add($workspace);
        }

        $channelConfiguration = $this->createChannelConfiguration($channel, $workspace);

        $this->channelConfigurationRepository->add($channelConfiguration);

        return [
            'channelConfiguration' => $channelConfiguration,
            'workspace' => $workspace,
        ];
    }

    public function createChannelConfiguration(ChannelInterface $channel, WorkspaceInterface $workspace = null): ChannelConfigurationInterface
    {
        $channelConfiguration = $this->channelConfigurationFactory->createNew();
        $channelConfiguration->setWorkspace($workspace);
        $channelConfiguration->setChannel($channel);

        return $channelConfiguration;
    }

    private function createWorkspace(): WorkspaceInterface
    {
        $workspace = $this->workspaceFactory->createNew();
        $workspace->setName(self::DEFAULT_WORKSPACE_NAME);
        $workspace->setApiKey(self::DEFAULT_API_KEY);

        return $workspace;
    }
}
