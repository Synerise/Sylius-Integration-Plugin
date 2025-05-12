<?php

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
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
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;

class CartItemAddProcessor
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function process(AddToCartCommandInterface $addToCartCommand): void
    {
        /** @var OrderInterface $cart */
        $cart = $addToCartCommand->getCart();

        $configuration = $this->configurationFactory->get($cart->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            AddedToCartBuilder::ACTION,
            $this->prepareCartRequestBody($cart, $cartItem),
            $cart->getChannel()?->getId()
        );
    }

    /**
     * @throws NotFoundException
     */
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

        $cartEvent = AddedToCartBuilder::initialize($this->identityManager->getClient())
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
