<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\ClientCartEventRequest;
use Synerise\Api\V4\Models\DiscountedUnitPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\RegularUnitPrice;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\RequestBody\Events\RemovedFromCartBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Webmozart\Assert\Assert;

class CartItemRemoveListener
{
    private ClientBuilder $clientBuilder;

    private IdentityManager $identityManager;

    public function __construct(
        ClientBuilder $clientBuilder,
        IdentityManager $identityManagerProvider
    ) {
        $this->clientBuilder = $clientBuilder;
        $this->identityManager = $identityManagerProvider;
    }

    public function process(GenericEvent $event): void
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $event->getSubject();

        Assert::isInstanceOf($cartItem, OrderItemInterface::class);

        /** @var OrderInterface $cart */
        $cart = $cartItem->getOrder();

        $requestBody = $this->prepareCartRequestBody($cart, $cartItem);
        $this->clientBuilder->v4()->events()->removedFromCart()->post($requestBody)->wait();
    }

    private function prepareCartRequestBody(OrderInterface $cart, OrderItemInterface $cartItem): ClientCartEventRequest
    {
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

        // @phpstan-ignore return.type
        return RemovedFromCartBuilder::initialize($this->identityManager->getClient())
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
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }
}
