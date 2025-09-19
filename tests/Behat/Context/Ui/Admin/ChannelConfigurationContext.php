<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;
use Webmozart\Assert\Assert;

final class ChannelConfigurationContext extends MinkContext
{
    /**
     * @param RepositoryInterface<ChannelInterface> $channelRepository
     * @param FactoryInterface<WorkspaceInterface> $workspaceFactory
     * @param RepositoryInterface<WorkspaceInterface> $workspaceRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $channelRepository,
        private FactoryInterface $workspaceFactory,
        private RepositoryInterface $workspaceRepository,
        private ContainerBagInterface $params
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
        $apiKey = $this->params->get('synerise.test.api_key');
        $workspace = $this->workspaceFactory->createNew();
        $workspace->setName($workspaceName);
        $workspace->setApiKey($apiKey);
        $this->workspaceRepository->add($workspace);
    }

    #[When('I wait for :locator element')]
    public function IWaitFor(string $locator): void
    {
        $this->getSession()->wait(1000, "document.querySelector('{$locator}') !== null");
    }

    #[When('I click :locator element')]
    public function iClick(string $locator): void
    {
        $element = $this->getSession()->getPage()->find('css', $locator);
        $element->click();
    }

    #[Then('the channelConfiguration should exist in repository')]
    public function theChannelConfigurationShouldExist(): void
    {
        $channel = $this->sharedStorage->get('channel');
        $configuration = $this->entityManager->getRepository(ChannelConfiguration::class)->findOneBy(['channel' => $channel]);
        Assert::notNull($configuration, sprintf('Channel Configuration of "%s" channel not found', $channel));
    }
}
