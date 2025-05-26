<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\LoggedInEvent;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class LoginRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = LoggedInEvent::class;

    /**
     * @param LoggedInEvent $payload
     * @return Promise<void|null>
     * @throws Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->events()->loggedIn()->post($payload);
    }
}
