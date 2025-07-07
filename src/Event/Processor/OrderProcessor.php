<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\OrderInterface;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource\OrderToTransactionMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class OrderProcessor
{
    public function __construct(
        private OrderToTransactionMapper $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private EventHandlerResolver $eventHandlerResolver,
        private ?IdentityManager $identityManager = null,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(OrderInterface $order): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType('transaction.charge')) {
            return;
        }

        Assert::NotNull($configuration->getChannel());
        Assert::NotNull($order->getCustomer()?->getEmail());

        if ($this->identityManager && $order->isCreatedByGuest()) {
            $this->identityManager->identify($order->getCustomer()->getEmail());
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            'transaction.charge',
            $this->mapper->prepare($order, 'live'),
            $configuration->getChannel()->getId(),
        );
    }
}
