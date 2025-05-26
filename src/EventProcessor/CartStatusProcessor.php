<?php

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Api\V4\Models\Product;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;

class CartStatusProcessor
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(OrderInterface $cart): void
    {
        $configuration = $this->configurationFactory->get($cart->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            CartStatusBuilder::ACTION,
            $this->prepareCartStatusRequestBody($cart),
            $cart->getChannel()?->getId(),
            []
        );
    }

    private function prepareCartStatusRequestBody(OrderInterface $cart): CustomEvent
    {
        $products = [];
        foreach ($cart->getItems() as $item) {
            $product = new Product();
            $product->setSku($item->getProduct()?->getCode());
            $product->setAdditionalData(['skuVariant' => $item->getVariant()?->getCode()]);
            $product->setQuantity($item->getQuantity());
            $products[] = $product;
        }

        $customEvent = CartStatusBuilder::initialize($this->identityManager->getClient())
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
