<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;

final class ChannelConfigurationContext implements Context
{
    public const DEFAULT_API_KEY = 'cd27ee0f-93e9-4bac-a927-c383d15de14f';

    /**
     * @param FactoryInterface<WorkspaceInterface> $workspaceFactory
     * @param RepositoryInterface<WorkspaceInterface> $workspaceRepository
     */
    public function __construct(
        private FactoryInterface $workspaceFactory,
        private RepositoryInterface $workspaceRepository,
    ) {
    }

    #[Given('the store has a workspace named :workspaceName')]
    public function theStoreHasAWorkspaceNamed(string $workspaceName): void
    {
        $workspace = $this->workspaceFactory->createNew();
        $workspace->setName($workspaceName);
        $workspace->setApiKey(self::DEFAULT_API_KEY);
        $this->workspaceRepository->add($workspace);
    }
}
