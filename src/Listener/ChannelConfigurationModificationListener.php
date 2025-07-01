<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\Api\Workspace\Models\TrackingCodeCreationByDomainRequest;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;

final readonly class ChannelConfigurationModificationListener
{
    public function __construct(private ClientBuilderFactory $clientBuilderFactory)
    {
    }

    public function getTrackingCodeRequest(ResourceControllerEvent $event): void
    {
        /** @var ChannelConfiguration $channelConfiguration */
        $channelConfiguration = $event->getSubject();

        if (!$channelConfiguration->isTrackingEnabled()) {
            return;
        }

        /** @var Workspace $workspace */
        $workspace = $channelConfiguration->getWorkspace();
        $clientBuilder = $this->clientBuilderFactory->create($workspace);

        try {
            if (!$channelConfiguration->getCookieDomain()) {
                $channelConfiguration->setCookieDomain($channelConfiguration->getChannel()?->getHostname());
            }

            $request = new TrackingCodeCreationByDomainRequest();
            $request->setDomain($channelConfiguration->getCookieDomain());

            $response = $clientBuilder->workspace()->tracker()->getOrCreateByDomain()->post($request)->wait();
            if ($response) {
                $channelConfiguration->setTrackingCode($response->getCode());
            } else {
                $event->stop('Tracking code request failed. Empty response');
            }
        } catch (\Exception $e) {
            $event->stop('Tracking code request failed');
        }
    }
}
