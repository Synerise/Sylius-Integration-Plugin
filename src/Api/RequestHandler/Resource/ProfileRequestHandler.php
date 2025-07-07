<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler\Resource;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Api\V4\Models\Profile;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\AbstractRequestHandler;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\BatchRequestHandlerInterface;
use Synerise\SyliusIntegrationPlugin\Model\Workspace\Mode;
use Webmozart\Assert\Assert;

class ProfileRequestHandler extends AbstractRequestHandler implements BatchRequestHandlerInterface
{
    /** @var class-string<Profile> */
    public static string $requestClass = Profile::class;

    /**
     * @param Profile $payload
     *
     * @throws \Exception
     */
    public function send(Parsable $payload, string|int $channelId): Promise
    {
        Assert::isInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        return $this->getClientBuilder($config)->v4()->clients()->post($payload);
    }

    /**
     * @param Profile[] $payload
     *
     * @throws \Exception
     */
    public function sendBatch(array $payload, int|string $channelId): Promise
    {
        Assert::allIsInstanceOf($payload, self::$requestClass);

        $config = $this->getChannelConfiguration($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $config->setMode(Mode::Scheduled);

        return $this->getClientBuilder($config)->v4()->clients()->batch()->post($payload);
    }
}
