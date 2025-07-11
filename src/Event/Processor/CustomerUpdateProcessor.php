<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\CustomerInterface;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource\CustomerToProfileMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class CustomerUpdateProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private CustomerToProfileMapper $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType('profile.update')) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerResolver->get($type)->processEvent(
            'profile.update',
            $this->mapper->prepare($customer, 'live'),
            $configuration->getChannel()->getId(),
        );
    }
}
