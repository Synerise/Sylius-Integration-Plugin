<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\DiscountAmount;
use Synerise\Api\V4\Models\DiscountPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\Product;
use Synerise\Api\V4\Models\RegularPrice;
use Synerise\Api\V4\Models\Revenue;
use Synerise\Api\V4\Models\Transaction;
use Synerise\Api\V4\Models\TransactionMeta;
use Synerise\Api\V4\Models\Value;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\BeforeTransactionRequestEvent;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class OrderProcessor
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager             $identityManager,
        private EventHandlerFactory         $eventHandlerFactory,
        private EventDispatcherInterface    $eventDispatcher
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process(OrderInterface $order): void
    {
        $configuration = $this->configurationFactory->get();
        if (!$type = $configuration?->getEventHandlerType("transaction.charge")) {
            return;
        }

        Assert::NotNull($configuration->getChannel());

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            "transaction.charge",
            $this->prepareTransaction($order),
            $configuration->getChannel()->getId(), []
        );
    }


    private function prepareTransaction(OrderInterface $order): Transaction
    {
        $customer = $order->getCustomer();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
            $client = new Client();
        }

        Assert::notNull($customer);

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $transaction = new Transaction();
        $transaction->setClient($client);
        $transaction->setOrderId((string)$order->getId());
        $transaction->setRecordedAt($order->getCheckoutCompletedAt()?->format(\DateTimeInterface::ATOM));

        $total = $order->getTotal();
        $taxTotal = $order->getTaxTotal();
        $promotionTotal = abs($order->getOrderPromotionTotal());
        $currency = $order->getCurrencyCode();

        $value = new Value();
        $value->setAmount(($total - $taxTotal) / 100);
        $value->setCurrency($currency);
        $transaction->setValue($value);

        $revenue = new Revenue();
        $revenue->setAmount($total / 100);
        $revenue->setCurrency($currency);
        $transaction->setRevenue($revenue);

        $discountAmount = new DiscountAmount();
        $discountAmount->setAmount($promotionTotal / 100);
        $discountAmount->setCurrency($currency);
        $transaction->setDiscountAmount($discountAmount);

        $metadata = new TransactionMeta();
        $metadata->setAdditionalData([
            "status" => $order->getState(),
            "discountCode" => $order->getPromotionCoupon()?->getCode(),
            "lastUpdateType" => "live"
        ]);
        $transaction->setMetadata($metadata);

        /** @var array<Product> $products */
        $products = [];
        /** @var OrderItemInterface $orderItem */
        foreach ($order->getItems() as $orderItem) {
            $products[] = $this->prepareTransactionProductData($orderItem);;
        }
        $transaction->setProducts($products);
        $transaction->setEventSalt($order->getNumber());

        $event = new BeforeTransactionRequestEvent($transaction, $order);
        $this->eventDispatcher->dispatch($event, BeforeTransactionRequestEvent::NAME);

        return $event->getTransaction();
    }

    private function prepareTransactionProductData(OrderItemInterface $orderItem): Product
    {
        $order = $orderItem->getOrder();
        Assert::implementsInterface($order, \Sylius\Component\Order\Model\OrderInterface::class);

        $orderProduct = $orderItem->getProduct();
        $currencyCode = $order->getCurrencyCode();
        $quantity = $orderItem->getQuantity();
        $category = $orderProduct?->getMainTaxon()?->getFullname(' > ');

        $product = new Product();
        $name = $orderItem->getProductName() . ($orderItem->getVariantName() ? ' - ' . $orderItem->getVariantName() : '');
        $product->setName($name);
        $product->setQuantity($quantity);
        $product->setSku($orderProduct?->getCode());

        $unitPrice = $orderItem->getUnitPrice();
        $originalUnitPrice = $orderItem->getOriginalUnitPrice();
        $discountedUnitPrice = $orderItem->getFullDiscountedUnitPrice();
        $unitTax = $orderItem->getTaxTotal() / $quantity;

        $regularPrice = new RegularPrice();
        $regularPrice->setCurrency($currencyCode);
        $regularPrice->setAmount($originalUnitPrice / 100);
        $product->setRegularPrice($regularPrice);

        $finalUnitPrice = new FinalUnitPrice();
        $finalUnitPrice->setCurrency($currencyCode);
        $finalUnitPrice->setAmount(($discountedUnitPrice + $unitTax) / 100);
        $product->setFinalUnitPrice($finalUnitPrice);

        $discountPrice = new DiscountPrice();
        $discountPrice->setCurrency($currencyCode);
        $discountPrice->setAmount(($unitPrice - $discountedUnitPrice) / 100);
        $product->setDiscountPrice($discountPrice);

        $product->setAdditionalData([
            "category" => $category,
        ]);

        return $product;
    }

}
