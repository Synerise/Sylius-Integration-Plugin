<?php

namespace Synerise\SyliusIntegrationPlugin\EventHandler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\EventMessage;

class MessageQueueHandler implements EventHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private SerializationWriterFactory $writerFactory
    ) {
    }

    public function processEvent(string $action, Parsable $payload, string $channelId): void
    {
        $this->messageBus->dispatch(new EventMessage($action, $this->serialize($payload), $channelId));
    }

    private function serialize(Parsable $payload): string
    {
        $writer = $this->writerFactory->getSerializationWriter('application/json');
        $payload->serialize($writer);
        $string = (string) $writer->getSerializedContent();
        if (!str_starts_with($string, '{')) {
            return sprintf('{ %s }', $string);
        }

        return $string;
    }

}
