<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Exception;
use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\Profile;
use Synerise\Sdk\Api\Config;
use Webmozart\Assert\Assert;

class ProfileBatchRequestHandler extends AbstractRequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass = Profile::class;

    /**
     * @param Profile[] $payload
     * @param Config $config
     * @param string|int|null $channelId
     * @param array $additionalData
     * @return Promise<void|null>
     * @throws Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId, array $additionalData): Promise
    {
        Assert::isArray($payload);

        return $this->getClientBuilder($config)->v4()->clients()->batch()->post($payload);
    }
}
