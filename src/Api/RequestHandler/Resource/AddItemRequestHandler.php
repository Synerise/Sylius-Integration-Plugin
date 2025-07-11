<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler\Resource;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\AbstractRequestHandler;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\BatchRequestHandlerInterface;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Mode;
use Webmozart\Assert\Assert;

class AddItemRequestHandler extends AbstractRequestHandler implements BatchRequestHandlerInterface
{
    /** @var class-string<AddItem> */
    public static string $requestClass = AddItem::class;

    /**
     * @param AddItem $payload
     *
     * @throws \Exception
     */
    public function send(Parsable $payload, string|int $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $catalogId = $this->getSynchronizationConfiguration($channelId)?->getCatalogId();
        Assert::notNull($catalogId);

        return $this->getClientBuilder($config)
            ->catalogs()->bags()->byCatalogId($catalogId)->items()->post($payload);
    }

    /**
     * @param AddItem[] $payload
     *
     * @throws \Exception
     */
    public function sendBatch(array $payload, int|string $channelId): Promise
    {
        Assert::allIsInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $config->setMode(Mode::Scheduled);

        $catalogId = $this->getSynchronizationConfiguration($channelId)?->getCatalogId();
        Assert::notNull($catalogId);

        return $this->getClientBuilder($config)
            ->catalogs()->bags()->byCatalogId($catalogId)->items()->batch()->post($payload);
    }
}
