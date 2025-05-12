<?php

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\CartEvent;
use Synerise\Api\V4\Models\DiscountedUnitPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\RegularUnitPrice;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Api\RequestBody\Events\RemovedFromCartBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class CartItemRemoveProcessor
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function process(OrderItemInterface $cartItem): void
    {
        Assert::isInstanceOf($cartItem, OrderItemInterface::class);

        /** @var OrderInterface $cart */
        $cart = $cartItem->getOrder();

        $configuration = $this->configurationFactory->get($cart->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            RemovedFromCartBuilder::ACTION,
            $this->prepareCartRequestBody($cart, $cartItem),
            $cart->getChannel()?->getId()
        );
    }

    private function prepareCartRequestBody(OrderInterface $cart, OrderItemInterface $cartItem): CartEvent
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

        $cartEvent = RemovedFromCartBuilder::initialize($this->identityManager->getClient())
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
            sprintf('synerise.%s.prepare', RemovedFromCartBuilder::ACTION)
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }
}
