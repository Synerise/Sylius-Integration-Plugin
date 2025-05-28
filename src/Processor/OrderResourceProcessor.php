<?php

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Resource\Model\ResourceInterface;
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
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;
use Webmozart\Assert\Assert;

class OrderResourceProcessor implements ResourceProcessorInterface
{
    /**
     * @param string $resourceType
     * @return bool
     */
    public function supports(string $resourceType): bool
    {
        return $resourceType == SynchronizationDataType::Order;
    }

    /**
     * @param OrderInterface $resource
     * @return Transaction
     */
    public function process(ResourceInterface $resource): Parsable
    {
        Assert::implementsInterface($resource, OrderInterface::class);

        return $this->prepareTransaction($resource);
    }

    private function prepareTransaction(OrderInterface $order): Transaction
    {
        $customer = $order->getCustomer();
        Assert::notNull($customer);

        $client = new Client();
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
            "discountCode" => $order->getPromotionCoupon()?->getCode()
        ]);
        $transaction->setMetadata($metadata);

        /** @var array<Product> $products */
        $products = [];
        foreach ($order->getItems() as $orderItem) {
            /** @var OrderItemInterface $orderItem */
            $products[] = $this->prepareTransactionProductData($orderItem);
        }
        $transaction->setProducts($products);
        $transaction->setEventSalt($order->getNumber());

        return $transaction;
    }

    private function prepareTransactionProductData(OrderItemInterface $orderItem): Product
    {
        $order = $orderItem->getOrder();
        Assert::implementsInterface($order, OrderInterface::class);

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
