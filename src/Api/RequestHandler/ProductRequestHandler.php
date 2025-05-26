<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class ProductRequestHandler extends AbstractRequestHandler
{
    public static string $requestClass = AddItem::class;

    public function send(Parsable $payload, Config $config, string|int|null $channelId, array $additionalData): Promise
    {
        Assert::keyExists($additionalData, 'catalog_id');
        Assert::notEmpty($additionalData['catalog_id']);
        Assert::isInstanceOf($payload, self::$requestClass);

        /** @var AddItem $payload */
        return $this->getClientBuilder($config)
            ->catalogs()->bags()->byCatalogId($additionalData['catalog_id'])->items()->post($payload);
    }
}
