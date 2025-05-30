<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Api\Catalogs\Models\AddItemValue;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Model\ProductUpdateRequestEvent;
use Webmozart\Assert\Assert;

class ProductToAddItemMapper implements RequestMapperInterface
{
    public function __construct(
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
        private EventDispatcherInterface            $eventDispatcher
    ) {
    }

    /**
     * @param ProductInterface $resource
     */
    public function prepare(
        ResourceInterface $resource,
        string $type = 'synchronization',
        ?ChannelInterface $channel = null
    ): AddItem
    {
        Assert::implementsInterface($resource, ProductInterface::class);
        Assert::notNull($channel);

        return $this->prepareProductUpdateRequest($resource, $channel);
    }

    private function prepareProductUpdateRequest(
        ProductInterface $product,
        ChannelInterface $channel
    ): AddItem
    {
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

        /** @var CoreChannelInterface $channel */
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

        $options = $product->getOptions();
        foreach ($options as $option) {
            $values = [];
            foreach($option->getValues() as $value) {
                $values[] = $this->getProductOptionValue(
                    $value,
                    $configuration->getProductAttributeValue()
                );
            }

            $additionalData[$option->getCode()] = $values;
        }

        $value = new AddItemValue();
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

    private function getProductOptionValue(
        ProductOptionValueInterface $attributeValue,
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
