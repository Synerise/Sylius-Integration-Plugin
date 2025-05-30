<?php

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
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

    private ClientBuilderFactory $clientBuilderFactory;

    public function __construct(ClientBuilderFactory $clientBuilderFactory)
    {
        $this->clientBuilderFactory = $clientBuilderFactory;
    }

    /**
     * @param ResourceControllerEvent $event
     * @return void
     */
    public function checkPermissionsRequest(ResourceControllerEvent $event): void
    {
        /** @var Workspace $workspace */
        $workspace = $event->getSubject();

        $clientBuilder = $this->clientBuilderFactory->create($workspace);
        try {
            $response = $clientBuilder->uauth()->apiKey()->permissionCheck()->post(self::REQUIRED_PERMISSIONS)->wait();
            if ($response && $response->getBusinessProfileName()) {
                $workspace->setName($response->getBusinessProfileName());
                $permissions = $response->getPermissions() ?: [];
                $missingPermissions = [];
                foreach($permissions as $permission => $isSet) {
                    if(!$isSet) {
                        $missingPermissions[] = $permission;
                    }
                }
                $workspace->setPermissions($missingPermissions);
            } else {
                $event->stop('Permissions check request failed. Empty response');
            }
        } catch (\Exception $e) {
            $event->stop('Permissions check request failed');
        }
    }
}
