<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Context\Setup\OrderContext as SetupOrderContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class OrderContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private SetupOrderContext $orderContext,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @Given /^there is a "([^"]+)" "(#\d+)" order with ("[^"]+" product) in (this channel) placed by (customer "[^"]+") at "([^"]+)"$/
     */
    public function thereIsAOrderWithProductAndThisChannel(
        string $state,
        string $orderNumber,
        ProductInterface $product,
        ChannelInterface $channel,
        CustomerInterface $customer,
        string $completedAt
    ): void {
        $this->orderContext->thereIsAOrderWithProduct($orderNumber, $product, $state, $channel);

        $order = $this->sharedStorage->get('order');
        $order->setCheckoutCompletedAt(new \DateTime($completedAt));
        $order->setCustomer($customer);

        $this->entityManager->flush();
        $this->sharedStorage->set('order', $order);
    }
}
