<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;
use Behat\Step\When;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;

final class ChannelConfigurationContext extends RawMinkContext
{
    public const DEFAULT_API_KEY = 'cd27ee0f-93e9-4bac-a927-c383d15de14f';

    /**
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     * @param FactoryInterface<WorkspaceInterface> $workspaceFactory
     * @param RepositoryInterface<WorkspaceInterface> $workspaceRepository
     */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $channelRepository,
        private FactoryInterface $workspaceFactory,
        private RepositoryInterface $workspaceRepository,
    ) {
    }

    #[Given('this channel has hostname :hostname')]
    public function ThisChannelHasHostname(string $hostname): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channel->setHostname($hostname);

        $this->channelRepository->add($channel);
    }

    #[Given('the store has a workspace named :workspaceName')]
    public function theStoreHasAWorkspaceNamed(string $workspaceName): void
    {
        $workspace = $this->workspaceFactory->createNew();
        $workspace->setName($workspaceName);
        $workspace->setApiKey(self::DEFAULT_API_KEY);
        $this->workspaceRepository->add($workspace);
    }

    #[When('I wait :timeMs ms')]
    public function IWaitMs(int $timeMs): void
    {
        \usleep($timeMs * 1000);
    }

    #[When('I click :locator element')]
    public function iClick(string $locator): void
    {
        $element = $this->getSession()->getPage()->find('css', $locator);
        $element->click();
    }
}
