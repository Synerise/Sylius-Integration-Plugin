<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\Transaction;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class TransactionRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = Transaction::class;

    /**
     * @param Transaction $payload
     * @return Promise<void|null>
     * @throws \Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->transactions()->post($payload);
    }
}
