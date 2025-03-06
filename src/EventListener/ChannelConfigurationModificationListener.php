<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfiguration;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;

class ChannelConfigurationModificationListener
{
    /**
     * @param ResourceControllerEvent $event
     * @return void
     */
    public function getTrackingCodeRequest($event)
    {
        /** @var ChannelConfiguration $channelConfiguration */
        $channelConfiguration = $event->getSubject();

        $workspace = $channelConfiguration->getWorkspace();
        if ($workspace) {
            $clientBuilder = new ClientBuilder($channelConfiguration->getWorkspace());
            try {
                $request = new \Synerise\Api\Workspace\Models\TrackingCodeCreationByDomainRequest();
                $request->setDomain('sylius.local');
                $response = $clientBuilder->workspace()->tracker()->getOrCreateByDomain()->post($request)->wait();
                if ($response) {
                    $contents = json_decode($response->getContents());
                    $channelConfiguration->setTrackingCode($contents->code);
//                $workspace->setName($response->getBusinessProfileName());
//                $permissions = $response->getPermissions() ?: [];
//                $missingPermissions = [];
//                foreach($permissions as $permission => $isSet) {
//                    if(!$isSet) {
//                        $missingPermissions[] = $permission;
//                    }
//                }
//                $workspace->setPermissions($missingPermissions);
                } else {
                    $event->stop('Permissions check request failed. Empty response');
                }
            } catch (\Exception $e) {
                $event->stop('Permissions check request failed');
            }
        }
    }
}
