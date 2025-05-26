<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\LoggedOutEvent;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class LogoutRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = LoggedOutEvent::class;

    /**
     * @param LoggedOutEvent $payload
     * @return Promise<void|null>
     * @throws Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->events()->loggedOut()->post($payload);
    }
}
