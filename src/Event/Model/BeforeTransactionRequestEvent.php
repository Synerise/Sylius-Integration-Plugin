<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Model;

use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Models\Transaction;

class BeforeTransactionRequestEvent extends Event
{
    public const NAME = 'synerise.transaction.charge.before_send';

    public function __construct(
        private Transaction $transaction,
        private OrderInterface $order,
    ) {
    }

    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }
}
