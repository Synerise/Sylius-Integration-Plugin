<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\Api\Workspace\Models\TrackingCodeCreationByDomainRequest;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;

class ChannelConfigurationModificationListener
{
    private ClientBuilderFactory $clientBuilderFactory;

    public function __construct(ClientBuilderFactory $clientBuilderFactory)
    {
        $this->clientBuilderFactory = $clientBuilderFactory;
    }

    /**
     * @param ResourceControllerEvent $event
     * @return void
     */
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
            $request = new TrackingCodeCreationByDomainRequest();
            $cookieDomain = $channelConfiguration->getCookieDomain();
            if (!$cookieDomain) {
                $cookieDomain = $channelConfiguration->getChannel()?->getHostname();
                $channelConfiguration->setCookieDomain($cookieDomain);
            }

            $request->setDomain($cookieDomain);
            $response = $clientBuilder->workspace()->tracker()->getOrCreateByDomain()->post($request)->wait();
            if ($response) {
                $contents = json_decode($response->getContents());
                // @phpstan-ignore-next-line
                $channelConfiguration->setTrackingCode($contents?->code);
            } else {
                $event->stop('Tracking code request failed. Empty response');
            }
        } catch (\Exception $e) {
            $event->stop('Tracking code request failed');
        }
    }

}
