<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Models\CartEvent;
use Synerise\Api\V4\Models\DiscountedUnitPrice;
use Synerise\Api\V4\Models\FinalUnitPrice;
use Synerise\Api\V4\Models\RegularUnitPrice;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Service\EventService;
use Webmozart\Assert\Assert;

final readonly class CartItemAddListener
{
    private IdentityManager $identityManager;

    private EventService $eventService;

    public function __construct(
        IdentityManager $identityManagerProvider,
        EventService $eventService
    ) {
        $this->identityManager = $identityManagerProvider;
        $this->eventService = $eventService;
    }

    /**
     * @throws NotFoundException|ExceptionInterface
     */
    public function process(GenericEvent $event): void
    {
        $addToCartCommand = $event->getSubject();
        Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);

        /** @var OrderInterface $cart */
        $cart = $addToCartCommand->getCart();

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();

        $this->eventService->processEvent(
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

        // @phpstan-ignore return.type
        return AddedToCartBuilder::initialize($this->identityManager->getClient())
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
