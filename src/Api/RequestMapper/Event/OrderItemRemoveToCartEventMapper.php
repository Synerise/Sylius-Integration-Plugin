<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\CartEvent;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\DiscountedUnitPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\RegularUnitPrice;
use Synerise\Sdk\Api\RequestBody\Events\RemovedFromCartBuilder;
use Synerise\SyliusIntegrationPlugin\Helper\ProductDataFormatter;

class OrderItemRemoveToCartEventMapper
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ProductDataFormatter $formatter,
    ) {
    }

    public function prepare(OrderItemInterface $cartItem, Client $client): CartEvent
    {
        /** @var \Sylius\Component\Core\Model\OrderInterface $cart */
        $cart = $cartItem->getOrder();

        $currencyCode = $cart->getCurrencyCode();

        /** @var ProductInterface $product */
        $product = $cartItem->getProduct();

        /** @var ProductVariantInterface $variant */
        $variant = $cartItem->getVariant();

        $regularUnitPrice = null;
        if ($cartItem->getOriginalUnitPrice()) {
            $regularUnitPrice = new RegularUnitPrice();
            $regularUnitPrice->setAmount($this->formatter->formatAmount($cartItem->getOriginalUnitPrice()));
            $regularUnitPrice->setCurrency($currencyCode);
        }

        $discountedUnitPrice = null;
        if ($cartItem->getUnitPrice() != $cartItem->getOriginalUnitPrice()) {
            $discountedUnitPrice = new DiscountedUnitPrice();
            $discountedUnitPrice->setAmount($this->formatter->formatAmount($cartItem->getUnitPrice()));
            $discountedUnitPrice->setCurrency($currencyCode);
        }

        $finalUnitPrice = new FinalUnitPrice();
        $finalUnitPrice->setAmount($this->formatter->formatAmount($cartItem->getDiscountedUnitPrice()));
        $finalUnitPrice->setCurrency($currencyCode);

        $cartEvent = RemovedFromCartBuilder::initialize($client)
            ->setQuantity($cartItem->getQuantity())
            ->setFinalUnitPrice($finalUnitPrice)
            ->setRegularUnitPrice($regularUnitPrice)
            ->setDiscountedUnitPrice($discountedUnitPrice)
            ->setSku($product->getCode())
            ->setName($product->getName())
            ->setCategory($this->formatter->formatTaxon($product->getMainTaxon()))
            ->setCategories($this->formatter->formatTaxonsCollection($product->getTaxons()) ?: null)
            ->setParam('skuVariant', $variant->getCode())
            ->build();

        $genericEvent = new GenericEvent($cartEvent, ['cart' => $cart, 'cartItem' => $cartItem]);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', RemovedFromCartBuilder::ACTION),
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }
}
