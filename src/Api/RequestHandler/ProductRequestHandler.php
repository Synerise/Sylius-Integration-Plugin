<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Webmozart\Assert\Assert;

class ProductRequestHandler extends AbstractRequestHandler
{
    public static string $requestClass = AddItem::class;

    public function __construct(
        ClientBuilderFactory $clientBuilderFactory,
        private SynchronizationConfigurationFactory $configurationFactory

    ) {
        parent::__construct($clientBuilderFactory);
    }

    public function send(Parsable $payload, Config $config, string|int|null $channelId): Promise
    {
        $configuration = $this->configurationFactory->get($channelId);
        Assert::notNull($configuration);

        $catalogId = $configuration->getCatalogId();
        Assert::notNull($catalogId);

        Assert::isInstanceOf($payload, self::$requestClass);

        /** @var AddItem $payload */
        return $this->getClientBuilder($config)->catalogs()->bags()->byCatalogId($catalogId)->items()->post($payload);
    }
}
