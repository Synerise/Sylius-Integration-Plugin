<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Api\V4\Models\Product;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;

class OrderToCartStatusEventMapper
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function prepare(Client $client, ?OrderInterface $cart = null): CustomEvent
    {
        if ($cart === null) {
            return $this->prepareEmptyCartStatus($client);
        } else {
            return $this->prepareCartStatus($cart, $client);
        }
    }

    private function prepareEmptyCartStatus(Client $client): CustomEvent
    {
        $customEvent = CartStatusBuilder::initialize($client)
            ->setTotalAmount(0)
            ->setTotalQuantity(0.0)
            ->setProducts([])
            ->build();

        $genericEvent = new GenericEvent($customEvent);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', CartStatusBuilder::ACTION)
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }

    private function prepareCartStatus(OrderInterface $cart, Client $client): CustomEvent
    {
        $products = [];
        foreach ($cart->getItems() as $item) {
            $product = new Product();
            $product->setSku($item->getProduct()?->getCode());
            $product->setAdditionalData(['skuVariant' => $item->getVariant()?->getCode()]);
            $product->setQuantity($item->getQuantity());
            $products[] = $product;
        }

        $customEvent = CartStatusBuilder::initialize($client)
            ->setTotalAmount($this->formatPrice($cart->getItemsTotal()))
            ->setTotalQuantity($cart->getTotalQuantity())
            ->setProducts($products)
            ->build();

        $genericEvent = new GenericEvent($customEvent, ['cart' => $cart]);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', CartStatusBuilder::ACTION)
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }

}
