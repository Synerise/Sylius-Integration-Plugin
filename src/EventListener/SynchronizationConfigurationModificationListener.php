<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\Api\Catalogs\Bags\BagsGetResponse;
use Synerise\Api\Catalogs\Models\AddBag;
use Synerise\Api\Workspace\Models\TrackingCodeCreationByDomainRequest;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;
use Webmozart\Assert\Assert;

final readonly class SynchronizationConfigurationModificationListener
{
    public const CATALOG_NAME_FORMAT = 'channel-%s';

    public function __construct(
        private ClientBuilderFactory $clientBuilderFactory,
        private ChannelConfigurationFactory $channelConfigurationFactory
    ) {
    }

    public function getCatalogIdRequest(ResourceControllerEvent $event): void
    {
        /** @var SynchronizationConfigurationInterface $synchronizationConfiguration */
        $synchronizationConfiguration = $event->getSubject();

        $channelId = $synchronizationConfiguration->getChannel()?->getId();

        $channelConfiguration = $this->channelConfigurationFactory->get($channelId);
        Assert::notNull($channelConfiguration);

        if (!$synchronizationConfiguration->getDataTypes()) {
            return;
        }

        $catalogName = sprintf(self::CATALOG_NAME_FORMAT, $channelId);
        $catalogId = null;

        /** @var Workspace $workspace */
        $workspace = $channelConfiguration->getWorkspace();
        $clientBuilder = $this->clientBuilderFactory->create($workspace);
        try {
            $response = $clientBuilder->catalogs()->bags()->get()->wait();
            if ($response) {
                foreach ($response->getData() ?: [] as $item) {
                    if ($item->getName() == $catalogName) {
                        $catalogId = $item->getId();
                        break;
                    }
                }
            } else {
                $event->stop('Catalog get request failed. Empty response');
            }

            if (!$catalogId) {
                $request = new AddBag();
                $request->setName($catalogName);

                $response = $clientBuilder->catalogs()->bags()->post($request)->wait();
                if ($response) {
                    $catalogId = $response->getData()?->getId();
                } else {
                    $event->stop('Catalog post request failed. Empty response');
                }
            }

            $synchronizationConfiguration->setCatalogId($catalogId);
        } catch (\Exception $e) {
            $event->stop('Catalog request request failed');
        }
    }

}
