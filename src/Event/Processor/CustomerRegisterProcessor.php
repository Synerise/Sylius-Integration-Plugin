<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\CustomerInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Sdk\Api\RequestBody\Events\RegisteredBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\CustomerToRegisteredEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class CustomerRegisterProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private CustomerToRegisteredEvent $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
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

        try {
            if ($customer->getEmail()) {
                $this->identityManager->identify($customer->getEmail());
            }
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
            $client = new Client();
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            RegisteredBuilder::ACTION,
            $this->mapper->prepare($customer, $client),
            $configuration->getChannel()->getId()
        );
    }
}
