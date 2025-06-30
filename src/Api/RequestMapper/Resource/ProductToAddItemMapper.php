<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Api\Catalogs\Models\AddItemValue;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Model\ProductUpdateRequestEvent;
use Synerise\SyliusIntegrationPlugin\Helper\ProductUrlHelper;
use Webmozart\Assert\Assert;

class ProductToAddItemMapper implements RequestMapperInterface
{
    public function __construct(
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
        private ProductUrlHelper                    $productUrlHelper,
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
        /** @var CoreChannelInterface $channel */
        $configuration = $this->synchronizationConfigurationFactory->get($channel->getId());
        Assert::notNull($configuration);

        $variant = $product->getEnabledVariants()->first();
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $additionalData = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'enabled' => $product->isEnabled(),
            'link' => $this->productUrlHelper->generate($product, $channel),
        ];

        $mainTaxon = $product->getMainTaxon();
        if ($mainTaxon) {
            $additionalData['category'] = $this->getCategoryValue(
                $mainTaxon,
                $configuration->getProductAttributeValue()
            );
        }

        $taxons = [];
        foreach ($product->getProductTaxons() as $productTaxon) {
            if ($productTaxon->getTaxon()) {
                /** @var array<string> $taxons */
                $taxons[] = $this->getCategoryValue(
                    $productTaxon->getTaxon(),
                    $configuration->getProductAttributeValue()
                );
            }
        }

        if (!empty($taxons)) {
            $additionalData['categories'] = $taxons;
        }

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

        /** @var string $attribute */
        foreach ($configuration->getProductAttributes() as $attribute) {
            if ($attributeValue = $product->getAttributeByCodeAndLocale($attribute)) {
                $additionalData[$attribute] = $this->getAttributeValue(
                    $attributeValue,
                    $configuration->getProductAttributeValue()
                );
            }
        }

        $options = $product->getOptions();
        foreach ($options as $option) {
            $values = [];
            foreach($option->getValues() as $value) {
                $values[] = $this->getOptionValue(
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

    private function getAttributeValue(
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

    private function getCategoryValue(
        TaxonInterface $taxon,
        ?ProductAttributeValue $config
    ): string|array
    {
        return match($config) {
            ProductAttributeValue::ID_VALUE => [
                'id' => $taxon->getId(),
                'value' => $taxon->getFullname(' > ')
            ],
            ProductAttributeValue::ID => $taxon->getId(),
            default => $taxon->getFullname(' > ')
        };
    }

    private function getOptionValue(
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
