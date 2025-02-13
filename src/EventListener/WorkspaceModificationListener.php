<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;

class WorkspaceModificationListener
{
    const REQUIRED_PERMISSIONS = [
        "API_CLIENT_CREATE",
        "API_BATCH_CLIENT_CREATE",
        "API_BATCH_TRANSACTION_CREATE",
        "API_TRANSACTION_CREATE",
        "API_CUSTOM_EVENTS_CREATE",
        "API_ADDED_TO_CART_EVENTS_CREATE",
        "API_REMOVED_FROM_CART_EVENTS_CREATE",
        "API_ADDED_TO_FAVORITES_EVENTS_CREATE",
        "API_LOGGED_IN_EVENTS_CREATE",
        "API_LOGGED_OUT_EVENTS_CREATE",
        "API_REGISTERED_EVENTS_CREATE",
        "CATALOGS_CATALOG_CREATE",
        "CATALOGS_CATALOG_READ",
        "CATALOGS_ITEM_BATCH_CATALOG_CREATE",
        "TRACKER_CREATE"
    ];

    /**
     * @param ResourceControllerEvent $event
     * @return void
     */
    public function checkPermissionsRequest($event)
    {
        /** @var Workspace $workspace */
        $workspace = $event->getSubject();

        $clientBuilder = new ClientBuilder($workspace);
        try {
            $clientBuilder->uauth()->apiKey()->permissionCheck()->post(self::REQUIRED_PERMISSIONS)->wait();
        } catch (\Exception $e) {
            $event->stop('Permissions check request failed');
        }
    }
}
