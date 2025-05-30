<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\RegisteredEvent;
use Synerise\Sdk\Api\RequestBody\Events\RegisteredBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Model\BeforeRegisterRequestEvent;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class CustomerRegisterProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType(RegisteredBuilder::ACTION)) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerResolver->get($type)->processEvent(
            RegisteredBuilder::ACTION,
            $this->prepareCustomerRegisteredRequestBody($customer),
            $configuration->getChannel()->getId()
        );
    }

    private function prepareCustomerRegisteredRequestBody(CustomerInterface $customer): RegisteredEvent
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $registeredEvent = RegisteredBuilder::initialize($client)->build();

        $event = new BeforeRegisterRequestEvent($registeredEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeRegisterRequestEvent::NAME);

        return $event->getRegisteredEvent();
    }
}
