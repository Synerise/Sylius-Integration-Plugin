<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler\Resource;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\Transaction;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\AbstractRequestHandler;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\BatchRequestHandlerInterface;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Mode;
use Webmozart\Assert\Assert;

class TransactionRequestHandler extends AbstractRequestHandler implements BatchRequestHandlerInterface
{
    /** @var class-string<Transaction> $requestClass */
    public static string $requestClass = Transaction::class;

    /**
     * @param Transaction $payload
     * @throws \Exception
     */
    public function send(Parsable $payload, string|int $channelId): Promise
    {
        Assert::IsInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        return $this->getClientBuilder($config)->v4()->transactions()->post($payload);
    }

    /**
     * @param Transaction[] $payload
     * @throws \Exception
     */
    public function sendBatch(array $payload, int|string $channelId): Promise
    {
        Assert::allIsInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $config->setMode(Mode::Scheduled);

        return $this->getClientBuilder($config)->v4()->transactions()->batch()->post($payload);
    }
}
