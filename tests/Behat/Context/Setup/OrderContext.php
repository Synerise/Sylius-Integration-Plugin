<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Context\Setup\OrderContext as SetupOrderContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class OrderContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private SetupOrderContext $orderContext
    ) {
    }

    /**
     * @Given there is a :state :orderNumber order with :product product in this channel
     */
    public function thereIsAOrderWithProductAndThisChannel(
        string $state,
        string $orderNumber,
        ProductInterface $product,
    ): void {
        $channel = $this->sharedStorage->get('channel');
        $this->orderContext->thereIsAOrderWithProduct($orderNumber, $product, $state, $channel);
    }
}
