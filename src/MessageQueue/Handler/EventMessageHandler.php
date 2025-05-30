<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Serialization\StringJsonParseNodeFactory;
use Synerise\SyliusIntegrationPlugin\Api\EventRequestHandlerFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\EventMessage;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
class EventMessageHandler
{
    public function __construct(
        private StringJsonParseNodeFactory  $parseNodeFactory,
        private EventRequestHandlerFactory  $requestHandlerFactory,
        private ChannelConfigurationFactory $configurationFactory
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(EventMessage $message): void
    {
        $requestHandler = $this->requestHandlerFactory->get($message->getAction());

        /** @var Config $config */
        $config = $this->configurationFactory->get($message->getSalesChannelId())?->getWorkspace();
        Assert::notNull($config);

        $payload = $this->deserialize($message->getPayload(), $requestHandler->getType());
        Assert::notNull($payload);

        $requestHandler->send(
            $payload,
            $message->getSalesChannelId()
        )->wait();
    }

    public static function getHandledMessages(): iterable
    {
        return [EventMessage::class];
    }

    /**
     * Gets the model object value of the node.
     * @template T of Parsable
     * @param string $serializedPayload
     * @param array{class-string<T>,string} $type The type for the Parsable object.
     * @return Parsable|null the model object value of the node.
     * @throws \Exception
     */
    public function deserialize(string $serializedPayload, array $type): ?Parsable
    {
        return $this->parseNodeFactory
            ->getRootParseNode($serializedPayload)
            ->getObjectValue($type);
    }
}
