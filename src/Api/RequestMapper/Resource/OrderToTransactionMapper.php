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
use Synerise\Sdk\Api\RequestBody\Models\ProductBuilder;
use Synerise\SDK\Api\RequestBody\Models\TransactionBuilder;
use Synerise\Sdk\Tracking\EventSourceProvider;
use Synerise\SyliusIntegrationPlugin\Helper\ProductDataFormatter;
use Webmozart\Assert\Assert;

class OrderToTransactionMapper implements RequestMapperInterface
{
    public function __construct(
        private ProductDataFormatter $formatter,
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

        $total = $resource->getTotal();
        $taxTotal = $resource->getTaxTotal();
        $promotionTotal = abs($resource->getOrderPromotionTotal());
        $currency = $resource->getCurrencyCode();

        $value = new Value();
        $value->setAmount($this->formatter->formatAmount($total - $taxTotal));
        $value->setCurrency($currency);

        $revenue = new Revenue();
        $revenue->setAmount($this->formatter->formatAmount($total));
        $revenue->setCurrency($currency);

        $discountAmount = new DiscountAmount();
        $discountAmount->setAmount($this->formatter->formatAmount($promotionTotal));
        $discountAmount->setCurrency($currency);

        $metadata = new TransactionMeta();
        $metadata->setAdditionalData([
            'status' => $resource->getState(),
            'discountCode' => $resource->getPromotionCoupon()?->getCode(),
            'lastUpdateType' => $type,
        ]);

        $transactionBuilder = TransactionBuilder::initialize()
            ->setClient($client)
            ->setOrderId((string) $resource->getId())
            ->setRecordedAt($resource->getCheckoutCompletedAt()?->format(\DateTimeInterface::ATOM))
            ->setValue($value)
            ->setRevenue($revenue)
            ->setDiscountAmount($discountAmount)
            ->setMetadata($metadata)
            ->setEventSalt($resource->getNumber());

        foreach ($resource->getItems() as $resourceItem) {
            /** @var OrderItemInterface $resourceItem */
            $transactionBuilder->addProduct($this->prepareTransactionProductData($resourceItem));
        }

        if ($this->sourceProvider) {
            $transactionBuilder->setSource($this->sourceProvider->getEventSource());
        }

        return $transactionBuilder->build();
    }

    private function prepareTransactionProductData(OrderItemInterface $resourceItem): Product
    {
        $resource = $resourceItem->getOrder();
        Assert::implementsInterface($resource, \Sylius\Component\Order\Model\OrderInterface::class);

        $resourceProduct = $resourceItem->getProduct();
        $currencyCode = $resource->getCurrencyCode();
        $quantity = $resourceItem->getQuantity();
        $name = $resourceItem->getProductName() . ($resourceItem->getVariantName() ? ' - ' . $resourceItem->getVariantName() : '');

        $unitPrice = $resourceItem->getUnitPrice();
        $originalUnitPrice = $resourceItem->getOriginalUnitPrice();
        $discountedUnitPrice = $resourceItem->getFullDiscountedUnitPrice();
        $unitTax = $resourceItem->getTaxTotal() / $quantity;

        $regularPrice = new RegularPrice();
        $regularPrice->setCurrency($currencyCode);
        $regularPrice->setAmount($originalUnitPrice ? $this->formatter->formatAmount($originalUnitPrice) : null);

        $finalUnitPrice = new FinalUnitPrice();
        $finalUnitPrice->setCurrency($currencyCode);
        $finalUnitPrice->setAmount($this->formatter->formatAmount($discountedUnitPrice + $unitTax));

        $discountPrice = new DiscountPrice();
        $discountPrice->setCurrency($currencyCode);
        $discountPrice->setAmount($this->formatter->formatAmount($unitPrice - $discountedUnitPrice));

        $productBuilder = ProductBuilder::initialize()
            ->setName($name)
            ->setQuantity($quantity)
            ->setSku($resourceProduct?->getCode())
            ->setRegularPrice($regularPrice)
            ->setFinalUnitPrice($finalUnitPrice)
            ->setDiscountPrice($discountPrice)
            ->setAdditionalData([
            'category' => $this->formatter->formatTaxon($resourceProduct?->getMainTaxon()),
        ]);

        return $productBuilder->build();
    }
}
