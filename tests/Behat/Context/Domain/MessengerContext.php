<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Worker;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Webmozart\Assert\Assert;

class MessengerContext implements Context
{
    public function __construct(
        private Connection $connection,
        private TransportInterface $transport,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Then('I should have :count message(s) in the queue')]
    public function iShouldHaveMessagesInTheQueue($count): void
    {
        Assert::eq($this->getQueueCount(), $count);
    }

    #[When('I process all message(s)')]
    #[When('I process :limit message(s)')]
    public function iProcessAllMessages(?int $limit = null): void
    {
        $this->processMessagesWithWorker($limit);
    }

    public function getQueueCount(): int
    {
        $sql = 'SELECT COUNT(*) FROM messenger_messages WHERE delivered_at IS NULL';
        return (int) $this->connection->fetchOne($sql);
    }

    public function processMessagesWithWorker(int $limit = null): int
    {
        $eventDispatcher = new EventDispatcher();

        if ($limit !== null) {
            $eventDispatcher->addSubscriber(
                new StopWorkerOnMessageLimitListener($limit)
            );
        } else {
            // Process all current messages
            $limit = $this->getQueueCount();
            if ($limit > 0) {
                $eventDispatcher->addSubscriber(
                    new StopWorkerOnMessageLimitListener($limit)
                );
            }
        }

        $worker = new Worker(
            [$this->transport],
            $this->messageBus,
            $eventDispatcher
        );

        $worker->run();

        return $limit ?? 0;
    }
}
