<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedOutEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedOutBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\CustomerToLoggedOutEvent;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Model\BeforeLogoutRequestEvent;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class CustomerLogoutProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private CustomerToLoggedOutEvent $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType(LoggedOutBuilder::ACTION)) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
            $client = new Client();
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            LoggedOutBuilder::ACTION,
            $this->mapper->prepare($customer, $client),
            $configuration->getChannel()->getId()
        );
    }
}
