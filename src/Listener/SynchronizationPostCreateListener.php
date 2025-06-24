<?php

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;

final readonly class SynchronizationPostCreateListener
{

    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $syneriseLogger,
    )
    {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();

        try{
            $this->messageBus->dispatch(new SyncStartMessage($synchronization->getId(), $synchronization->getType()->value));
        } catch (ExceptionInterface $e) {
            $this->syneriseLogger->error($e);
        }
    }

}
