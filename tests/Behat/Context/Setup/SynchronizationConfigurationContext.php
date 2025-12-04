<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Step\Given;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;

class SynchronizationConfigurationContext extends RawMinkContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $repository,
        private FactoryInterface $factory,
        private RepositoryInterface $attributeRepository,
    ) {
    }

    /**
     * @Given /^there is a already configured synchronization configuration for (this channel)$/
     */
    public function thereIsSynchronizationConfiguration($channel): void
    {
        $synchronizationConfiguration = $this->createSynchronizationConfiguration($channel);
        $this->sharedStorage->set('synchronizationConfiguration', $synchronizationConfiguration);
    }

    /**
     * @Given /^the synchronization configuration is configured with settings:$/
     */
    public function theChannelIsConfiguredWithSettings(TableNode $table): void
    {
        /** @var SynchronizationConfigurationInterface $synchronizationConfiguration */
        $synchronizationConfiguration = $this->sharedStorage->get('synchronizationConfiguration');

        foreach ($table->getRowsHash() as $key => $value) {
            switch ($key):
                case 'productAttributes':
                    $productAttributes = [];

                    foreach (explode(',', $value) as $attributeCode) {
                        $attribute = $this->attributeRepository->findOneBy(['code' => $attributeCode]);
                        $productAttributes[] = $attribute;
                    }

                    $synchronizationConfiguration->setProductAttributes($productAttributes);
                    break;
                case 'catalogId':
                    $synchronizationConfiguration->setCatalogId($value);
                    break;
                case 'productAttributeValue':
                    $synchronizationConfiguration->setProductAttributeValue(ProductAttributeValue::from($value));
                    break;
                default:
                    throw new \Exception("Undefined key $key");
            endswitch;
        }

        $this->saveSynchronizationConfiguration($synchronizationConfiguration);
    }

    /**
     * @Given /^there is a synerise catalog for (this channel)$/
     */
    public function theCatalogExist($channel): void
    {
        $this->sharedStorage->set('channel', $channel);
        $this->getSession()->setCookie('channelId', $channel->getId());
    }

    /**
     * @Given /^(?:|I )am on (saved synchronization configuration) page$/
     */
    public function visitSavedSynchronizationConfiguration(SynchronizationConfiguration $synchronizationConfiguration)
    {
        $this->visitPath("/admin/synerise/synchronization_configuration/".$synchronizationConfiguration->getId());
    }

    private function createSynchronizationConfiguration(?ChannelInterface $channel): SynchronizationConfigurationInterface
    {
        if (null === $channel && $this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
        }

        $synchronizationConfiguration = $this->factory->createNew();
        $synchronizationConfiguration->setChannel($channel);

        return $synchronizationConfiguration;
    }

    private function saveSynchronizationConfiguration(SynchronizationConfigurationInterface $synchronizationConfiguration): void
    {
        $this->repository->add($synchronizationConfiguration);
        $this->sharedStorage->set('synchronizationConfiguration', $synchronizationConfiguration);
    }
}
