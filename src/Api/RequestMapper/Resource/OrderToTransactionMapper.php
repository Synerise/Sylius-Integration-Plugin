<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Channel\Model\ChannelInterface;
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
use Synerise\Sdk\Tracking\EventSourceProvider;
use Webmozart\Assert\Assert;

class OrderToTransactionMapper implements RequestMapperInterface
{
    public function __construct(
        private ?EventSourceProvider $sourceProvider = null,
    ) {
    }

    /**
     * @param OrderInterface $resource
     */
    public function prepare(
        ResourceInterface $resource,
        string $type = 'synchronization',
        ?ChannelInterface $channel = null,
    ): Transaction {
        Assert::implementsInterface($resource, OrderInterface::class);

        $customer = $resource->getCustomer();
        Assert::notNull($customer);

        $client = new Client();
        $client->setEmail($customer->getEmail());
        $client->setCustomId((string) $customer->getId());

        $transaction = new Transaction();
        $transaction->setClient($client);
        $transaction->setOrderId((string) $resource->getId());
        $transaction->setRecordedAt($resource->getCheckoutCompletedAt()?->format(\DateTimeInterface::ATOM));

        $total = $resource->getTotal();
        $taxTotal = $resource->getTaxTotal();
        $promotionTotal = abs($resource->getOrderPromotionTotal());
        $currency = $resource->getCurrencyCode();

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
            'status' => $resource->getState(),
            'discountCode' => $resource->getPromotionCoupon()?->getCode(),
            'lastUpdateType' => $type,
        ]);
        $transaction->setMetadata($metadata);

        /** @var array<Product> $products */
        $products = [];
        foreach ($resource->getItems() as $resourceItem) {
            /** @var OrderItemInterface $resourceItem */
            $products[] = $this->prepareTransactionProductData($resourceItem);
        }
        if ($this->sourceProvider) {
            $transaction->setSource($this->sourceProvider->getEventSource());
        }
        $transaction->setProducts($products);
        $transaction->setEventSalt($resource->getNumber());

        return $transaction;
    }

    private function prepareTransactionProductData(OrderItemInterface $resourceItem): Product
    {
        $resource = $resourceItem->getOrder();
        Assert::implementsInterface($resource, \Sylius\Component\Order\Model\OrderInterface::class);

        $resourceProduct = $resourceItem->getProduct();
        $currencyCode = $resource->getCurrencyCode();
        $quantity = $resourceItem->getQuantity();
        $category = $resourceProduct?->getMainTaxon()?->getFullname(' > ');

        $product = new Product();
        $name = $resourceItem->getProductName() . ($resourceItem->getVariantName() ? ' - ' . $resourceItem->getVariantName() : '');
        $product->setName($name);
        $product->setQuantity($quantity);
        $product->setSku($resourceProduct?->getCode());

        $unitPrice = $resourceItem->getUnitPrice();
        $originalUnitPrice = $resourceItem->getOriginalUnitPrice();
        $discountedUnitPrice = $resourceItem->getFullDiscountedUnitPrice();
        $unitTax = $resourceItem->getTaxTotal() / $quantity;

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
            'category' => $category,
        ]);

        return $product;
    }
}
