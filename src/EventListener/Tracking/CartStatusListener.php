<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Events\Custom\CustomPostRequestBody;
use Synerise\Api\V4\Models\Product;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Tracking\IdentityManager;

class CartStatusListener
{
    private CartContextInterface $cartContext;

    private ClientBuilder $clientBuilder;

    private IdentityManager $identityManager;

    public function __construct(
        CartContextInterface $cartContext,
        ClientBuilder $clientBuilder,
        IdentityManager $identityManager
    ) {
        $this->cartContext = $cartContext;
        $this->clientBuilder = $clientBuilder;
        $this->identityManager = $identityManager;
    }

    /**
     * @throws \Exception
     */
    public function process(GenericEvent $event): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartContext->getCart();

        $requestBody = $this->prepareCartStatusRequestBody($cart);
        $this->clientBuilder->v4()->events()->custom()->post($requestBody)->wait();
    }

    private function prepareCartStatusRequestBody(OrderInterface $cart): CustomPostRequestBody
    {
        $products = [];
        foreach ($cart->getItems() as $item) {
            $product = new Product();
            $product->setSku($item->getProduct()?->getCode());
            $product->setAdditionalData(['skuVariant' => $item->getVariant()?->getCode()]);
            $product->setQuantity($item->getQuantity());
            $products[] = $product;
        }

        return CartStatusBuilder::initialize($this->identityManager->getClient())
            ->setTotalAmount($this->formatPrice($cart->getItemsTotal()))
            ->setTotalQuantity($cart->getTotalQuantity())
            ->setProducts($products)
            ->build();
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }
}
