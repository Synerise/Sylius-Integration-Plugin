<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class CheckPermissionSuccess implements ResponseFactoryInterface
{
    public function create(): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'businessProfileName' => 'Magento Plugin Local',
            'permissions' => [
                'API_REMOVED_FROM_CART_EVENTS_CREATE' => true,
                'API_BATCH_CLIENT_CREATE' => true,
                'API_CUSTOM_EVENTS_CREATE' => true,
                'API_CLIENT_CREATE' => true,
                'API_REGISTERED_EVENTS_CREATE' => true,
                'API_BATCH_TRANSACTION_CREATE' => true,
                'TRACKER_CREATE' => true,
                'API_TRANSACTION_CREATE' => true,
                'API_ADDED_TO_FAVORITES_EVENTS_CREATE' => true,
                'API_LOGGED_OUT_EVENTS_CREATE' => true,
                'API_ADDED_TO_CART_EVENTS_CREATE' => true,
                'CATALOGS_CATALOG_READ' => true,
                'CATALOGS_ITEM_BATCH_CATALOG_CREATE' => true,
                'API_LOGGED_IN_EVENTS_CREATE' => true,
                'CATALOGS_CATALOG_CREATE' => true,
                'CATALOGS_ITEM_BATCH_CATALOG_UPDATE' => true,
            ]
        ]));
    }
}
