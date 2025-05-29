<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Sylius\Component\Channel\Model\ChannelInterface;
use Synerise\Api\V4\Models\Profile;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Webmozart\Assert\Assert;

class BatchCustomerRequestHandler implements BatchRequestHandlerInterface
{
    /** @var class-string<object> $requestClass */
    public static string $requestClass = Profile::class;

    public function __construct(
        private ClientBuilderFactory $clientBuilderFactory,
        private ChannelConfigurationFactory $channelConfigurationFactory,
    )
    {
    }

    /**
     * @param Profile[] $payload
     * @throws \Exception
     */
    public function send(array $payload, ChannelInterface $channel): Promise
    {
        $config = $this->channelConfigurationFactory->get($channel->getId())?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        Assert::allIsInstanceOf($payload, self::$requestClass);

        return $this->clientBuilderFactory->create($config)->v4()->clients()->batch()->post($payload);
    }
}
