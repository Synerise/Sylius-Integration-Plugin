<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class CustomEventRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = CustomEvent::class;

    /**
     * @param array $additionalData
     * @param CustomEvent $payload
     * @return Promise<void|null>
     * @throws \Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId, array $additionalData): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->events()->custom()->post($payload);
    }
}
