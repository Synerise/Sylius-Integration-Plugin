<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Clients\ClientsPostRequestBody;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class ProfileRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = ClientsPostRequestBody::class;

    /**
     * @param ClientsPostRequestBody $payload
     * @return Promise<void|null>
     * @throws Exception
     */
    public function send(Parsable $payload, Config $config): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        return $this->getClientBuilder($config)->v4()->clients()->post($payload);
    }
}
