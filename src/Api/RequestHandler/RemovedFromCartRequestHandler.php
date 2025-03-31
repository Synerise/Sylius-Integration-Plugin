<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\ClientCartEventRequest;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class RemovedFromCartRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = ClientCartEventRequest::class;

    /**
     * @param ClientCartEventRequest $payload
     * @return Promise<void|null>
     * @throws \Exception
     */
    public function send(Parsable $payload, Config $config): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->events()->removedFromCart()->post($payload);
    }
}
