<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Serialization\StringJsonParseNodeFactory;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandlerFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\EventMessage;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
class EventMessageHandler
{
    private StringJsonParseNodeFactory $parseNodeFactory;

    private RequestHandlerFactory $requestHandlerFactory;

    private EntityRepository $repository;

    public function __construct(
        StringJsonParseNodeFactory $parseNodeFactory,
        RequestHandlerFactory $requestHandlerFactory,
        EntityRepository $repository
    )
    {
        $this->parseNodeFactory = $parseNodeFactory;
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->repository = $repository;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(EventMessage $message): void
    {
        $requestHandler = $this->requestHandlerFactory->get($message->getAction());

        /** @var Config $config */
        $config = $this->getConfigurationByChannel($message->getSalesChannelId())?->getWorkspace();
        Assert::notNull($config);

        $payload = $this->deserialize($message->getPayload(), $requestHandler->getType());
        Assert::notNull($payload);

        $requestHandler->send($payload, $config);
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

    /** @todo: move to separate service with cache */
    private function getConfigurationByChannel(string $channel): ?ChannelConfigurationInterface
    {
        // @phpstan-ignore return.type
        return $this->repository->findOneBy(
            ['channel' => $channel]
        );
    }
}
