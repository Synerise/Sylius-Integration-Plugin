<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler\Event;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\AbstractRequestHandler;
use Webmozart\Assert\Assert;

class ProductAddReviewRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = CustomEvent::class;

    /**
     * @param CustomEvent $payload
     * @return Promise<void|null>
     * @throws \Exception
     */
    public function send(Parsable $payload, string|int $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        return $this->getClientBuilder($config)->v4()->events()->custom()->post($payload);
    }
}
