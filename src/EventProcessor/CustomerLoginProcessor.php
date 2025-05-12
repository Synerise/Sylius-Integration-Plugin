<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Exception;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedInEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedInBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\BeforeLoginRequestEvent;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class CustomerLoginProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(CustomerInterface $customer): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType(LoggedInBuilder::ACTION)) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            LoggedInBuilder::ACTION,
            $this->prepareLoggedInEvent($customer),
            $configuration->getChannel()->getId()
        );
    }

    private function prepareLoggedInEvent(CustomerInterface $customer): LoggedInEvent
    {
        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
            $client = new Client();
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $loggedInEvent = LoggedInBuilder::initialize($client)->build();

        $event = new BeforeLoginRequestEvent($loggedInEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLoginRequestEvent::NAME);

        return $event->getLoggedInEvent();
    }
}
