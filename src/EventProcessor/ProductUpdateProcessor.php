<?php

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Api\Catalogs\Models\AddItem_value;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Event\ProductUpdateRequestEvent;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;
use Webmozart\Assert\Assert;

class ProductUpdateProcessor implements ProductProcessorInterface
{
    public function __construct(
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function process(ProductInterface $product): void
    {
        foreach ($product->getChannels() as $channel) {
            Assert::isInstanceOf($channel, ChannelInterface::class);

            $synchronizationConfiguration = $this->synchronizationConfigurationFactory->get($channel->getId());
            Assert::notNull($synchronizationConfiguration);

            $configuration = $this->channelConfigurationFactory->get();
            if (!$type = $configuration?->getEventHandlerType("product.update")) {
                return;
            }

            $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
                "product.update",
                $this->prepareProductUpdateRequest($product, $synchronizationConfiguration),
                $channel->getId(),
                [
                    'catalog_id' => $synchronizationConfiguration->getCatalogId()
                ]
            );
        }
    }

    private function prepareProductUpdateRequest(
        ProductInterface $product,
        SynchronizationConfigurationInterface $synchronizationConfiguration
    ): AddItem
    {
        $channel = $synchronizationConfiguration->getChannel();
        Assert::notNull($channel);

        $configuration = $this->synchronizationConfigurationFactory->get($channel->getId());
        Assert::notNull($configuration);

        $variant = $product->getEnabledVariants()->first();
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $additionalData = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'enabled' => $product->isEnabled(),
        ];

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        if ($channelPricing) {
            $price = $channelPricing->getPrice() ? $this->formatPrice($channelPricing->getPrice()) : null;
            if ($price) {
                $additionalData['price'] = $price;
            }

            $originalPrice = $channelPricing->getOriginalPrice() ? $this->formatPrice($channelPricing->getOriginalPrice()) : null;
            if ($originalPrice) {
                $additionalData['originalPrice'] = $originalPrice;
            }
        }

        $image = $this->getMainImage($product)?->getPath();
        if ($image) {
            $additionalData['image'] = $image;
        }

        foreach ($configuration->getProductAttributes() as $attribute) {
            if ($attributeValue = $product->getAttributeByCodeAndLocale($attribute)) {
                $additionalData[$attribute] = $this->getProductAttributeValue(
                    $attributeValue,
                    $configuration->getProductAttributeValue()
                );
            }
        }

        $value = new AddItem_value();
        $value->setAdditionalData($additionalData);

        $request = new AddItem();
        $request->setItemKey($product->getCode());
        $request->setValue($value);

        $event = new ProductUpdateRequestEvent($request, $product, $channel);
        $this->eventDispatcher->dispatch($event, ProductUpdateRequestEvent::NAME);

        return $event->getAddItem();
    }

    private function getMainImage(ProductInterface $product): ?ImageInterface
    {
        return $product->getImagesByType('main')->first() ?: null;
    }

    private function formatPrice(int $amount): float
    {
        return abs($amount / 100);
    }

    private function getProductAttributeValue(
        AttributeValueInterface $attributeValue,
        ?ProductAttributeValue $config): string|array
    {
        return match($config) {
            ProductAttributeValue::ID_VALUE => [
                'id' => $attributeValue->getId(),
                'value' => $attributeValue->getValue()
            ],
            ProductAttributeValue::ID => $attributeValue->getId(),
            default => $attributeValue->getValue()
        };
    }
}
