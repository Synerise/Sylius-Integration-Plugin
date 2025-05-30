<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\CartEvent;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\DiscountedUnitPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\RegularUnitPrice;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;

class OrderItemAddToCartEventMapper
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
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
            $regularUnitPrice->setAmount($this->formatPrice($cartItem->getOriginalUnitPrice()));
            $regularUnitPrice->setCurrency($currencyCode);
        }

        $discountedUnitPrice = null;
        if ($cartItem->getUnitPrice() != $cartItem->getOriginalUnitPrice()) {
            $discountedUnitPrice = new DiscountedUnitPrice();
            $discountedUnitPrice->setAmount($this->formatPrice($cartItem->getUnitPrice()));
            $discountedUnitPrice->setCurrency($currencyCode);
        }

        $finalUnitPrice = new FinalUnitPrice();
        $finalUnitPrice->setAmount($this->formatPrice($cartItem->getDiscountedUnitPrice()));
        $finalUnitPrice->setCurrency($currencyCode);

        /** @var TaxonInterface $mainTaxon */
        $mainTaxon = $product->getMainTaxon();

        $taxons = [];
        foreach ($product->getProductTaxons() as $productTaxon) {
            if ($productTaxon->getTaxon()) {
                /** @var array<string> $taxons */
                $taxons[] = $productTaxon->getTaxon()->getFullname(' > ');
            }
        }

        $cartEvent = AddedToCartBuilder::initialize($client)
            ->setQuantity($cartItem->getQuantity())
            ->setFinalUnitPrice($finalUnitPrice)
            ->setRegularUnitPrice($regularUnitPrice)
            ->setDiscountedUnitPrice($discountedUnitPrice)
            ->setSku($product->getCode())
            ->setName($product->getName())
            ->setCategory($mainTaxon->getFullname(' > '))
            ->setCategories($taxons ?: null)
            ->setParam('skuVariant', $variant->getCode())
            ->build();

        $genericEvent = new GenericEvent($cartEvent, ['cart' => $cart, 'cartItem' => $cartItem]);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', AddedToCartBuilder::ACTION)
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }
}
