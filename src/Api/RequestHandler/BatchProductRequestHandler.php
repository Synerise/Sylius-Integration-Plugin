<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Sylius\Component\Channel\Model\ChannelInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Webmozart\Assert\Assert;

class BatchProductRequestHandler implements BatchRequestHandlerInterface
{
    /** @var class-string<object> $requestClass */
    public static string $requestClass = AddItem::class;

    public function __construct(
        private ClientBuilderFactory $clientBuilderFactory,
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory
    )
    {
    }

    /**
     * @param AddItem[] $payload
     * @throws \Exception
     */
    public function send(array $payload, ChannelInterface $channel): Promise
    {
        $config = $this->channelConfigurationFactory->get($channel->getId())?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $catalogId = $this->synchronizationConfigurationFactory->get($channel->getId())?->getCatalogId();
        Assert::notNull($catalogId);

        Assert::allIsInstanceOf($payload, self::$requestClass);

        return $this->clientBuilderFactory->create($config)
            ->catalogs()->bags()->byCatalogId($catalogId)->items()->batch()->post($payload);
    }
}
