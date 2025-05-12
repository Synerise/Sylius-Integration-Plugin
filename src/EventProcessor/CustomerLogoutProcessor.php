<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedOutEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedOutBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\BeforeLogoutRequestEvent;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class CustomerLogoutProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType(LoggedOutBuilder::ACTION)) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            LoggedOutBuilder::ACTION,
            $this->prepareLoggedOutRequestBody($customer),
            $configuration->getChannel()->getId()
        );
    }

    private function prepareLoggedOutRequestBody(CustomerInterface $customer): LoggedOutEvent
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $loggedOutEvent = LoggedOutBuilder::initialize($client)->build();

        $event = new BeforeLogoutRequestEvent($loggedOutEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLogoutRequestEvent::NAME);

        return $event->getLoggedOutEvent();
    }
}
