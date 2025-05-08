<?php

namespace Synerise\SyliusIntegrationPlugin\Service;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandlerFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\EventMessage;
use Webmozart\Assert\Assert;

class EventService
{
    private RequestHandlerFactory $requestHandlerFactory;

    private EntityRepository $repository;

    private MessageBusInterface $messageBus;

    private SerializationWriterFactory $writerFactory;

    public function __construct(
        RequestHandlerFactory $requestHandlerFactory,
        EntityRepository $repository,
        MessageBusInterface $messageBus,
        SerializationWriterFactory $writerFactory
    )
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->repository = $repository;
        $this->messageBus = $messageBus;
        $this->writerFactory = $writerFactory;
    }

    /**
     * @throws \Exception
     */
    public function processEvent(string $action, Parsable $payload, string $channelId): void
    {
        if (!$this->isEnabled($channelId, $action)) {
            return;
        }

        if ($this->isEnabledForMessageQueue($channelId, $action)){
            $this->messageBus->dispatch(new EventMessage($action, $this->serialize($payload), $channelId));
        } else {
            $this->sendEvent($action, $payload, $channelId);
        }
    }

    /**
     * @throws \Exception
     */
    public function sendEvent(string $action, Parsable $payload, string $channelId): void
    {
        $requestHandler = $this->requestHandlerFactory->get($action);

        /** @var Config $config */
        $config = $this->getConfigurationByChannel($channelId)?->getWorkspace();
        Assert::notNull($config);

        $requestHandler->send($payload, $config);
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

    private function getConfigurationByChannel(string $channelId): ?ChannelConfigurationInterface
    {
        if (!isset($this->channels[$channelId])) {
            // @phpstan-ignore return.type
            return $this->repository->findOneBy(
                ['channel' => $channelId]
            );
        }

        return $this->channels;
    }

    private function isEnabledForMessageQueue(string $channelId, string $action): bool
    {
        $events = $this->getConfigurationByChannel($channelId)?->getQueueEvents();
        return is_array($events) && in_array($action, $events);
    }

    private function isEnabled(string $channelId, string $action): bool
    {
        $events = $this->getConfigurationByChannel($channelId)?->getEvents();
        return is_array($events) && in_array($action, $events);
    }
}
