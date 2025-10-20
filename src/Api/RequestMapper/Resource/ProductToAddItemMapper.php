<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\Api\Catalogs\Models\AddItemValue;
use Synerise\SyliusIntegrationPlugin\Entity\ProductAttributeValue;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Model\ProductUpdateRequestEvent;
use Synerise\SyliusIntegrationPlugin\Helper\ProductDataFormatter;
use Webmozart\Assert\Assert;

class ProductToAddItemMapper implements RequestMapperInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private SynchronizationConfigurationFactory $configurationFactory,
        private ProductDataFormatter $formatter,
    ) {
    }

    /**
     * @param ProductInterface $resource
     */
    public function prepare(
        ResourceInterface $resource,
        string $type = 'synchronization',
        ?ChannelInterface $channel = null,
    ): AddItem {
        Assert::implementsInterface($resource, ProductInterface::class);
        Assert::implementsInterface($channel, CoreChannelInterface::class);

        $configuration = $this->configurationFactory->get($channel->getId());
        Assert::notNull($configuration);

        $variant = $resource->getEnabledVariants()->first();
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $additionalData = [
            'id' => $resource->getId(),
            'itemId' => $resource->getCode(),
            'title' => $resource->getName(),
            'enabled' => $resource->isEnabled(),
            'link' => $this->formatter->generateUrl($resource, $channel),
        ];

        if ($mainTaxon = $resource->getMainTaxon()) {
            $additionalData['category'] = $this->getCategoryValue(
                $mainTaxon,
                $configuration->getProductAttributeValue(),
            );
        }

        $taxons = $resource->getProductTaxons()
            ->map(fn ($resourceTaxon) => $this->getCategoryValue(
                $resourceTaxon->getTaxon(),
                $configuration->getProductAttributeValue(),
            ))
            ->toArray();

        if (!empty($taxons)) {
            $additionalData['categories'] = $taxons;
        }

        $channelPricing = $variant->getChannelPricingForChannel($channel);
        if ($channelPricing) {
            $price = $channelPricing->getPrice() ? $this->formatter->formatAmount($channelPricing->getPrice()) : null;
            if ($price) {
                $additionalData['salePrice'] = ['value' => $price];
            }

            $originalPrice = $channelPricing->getOriginalPrice() ? $this->formatter->formatAmount($channelPricing->getOriginalPrice()) : null;
            if ($originalPrice) {
                $additionalData['price'] = ['value' => $originalPrice];
            }
        }

        if ($image = $this->formatter->getMainImageUrl($resource)) {
            $additionalData['imageLink'] = $image;
        }

        /** @var ProductAttribute $attribute */
        foreach ($configuration->getProductAttributes() as $attribute) {
            if ($attributeValue = $resource->getAttributeByCodeAndLocale($attribute->getCode())) {
                $additionalData[$attribute->getCode()] = $this->getAttributeValue(
                    $attributeValue,
                    $configuration->getProductAttributeValue(),
                );
            }
        }

        $options = $resource->getOptions();
        foreach ($options as $option) {
            $values = [];
            foreach ($option->getValues() as $value) {
                $values[] = $this->getOptionValue(
                    $value,
                    $configuration->getProductAttributeValue(),
                );
            }

            $additionalData[$option->getCode()] = $values;
        }

        $value = new AddItemValue();
        $value->setAdditionalData($additionalData);

        $request = new AddItem();
        $request->setItemKey($resource->getCode());
        $request->setValue($value);

        $event = new ProductUpdateRequestEvent($request, $resource, $channel);
        $this->eventDispatcher->dispatch($event, ProductUpdateRequestEvent::NAME);

        return $event->getAddItem();
    }

    private function getAttributeValue(
        AttributeValueInterface $attributeValue,
        ?ProductAttributeValue $config,
    ): string|float|int|bool|array {
        return match ($config) {
            ProductAttributeValue::ID_VALUE => [
                'id' => $attributeValue->getId(),
                'value' => $attributeValue->getValue(),
            ],
            ProductAttributeValue::ID => $attributeValue->getId(),
            default => $attributeValue->getValue()
        };
    }

    private function getCategoryValue(
        TaxonInterface $taxon,
        ?ProductAttributeValue $config,
    ): string|int|array {
        return match ($config) {
            ProductAttributeValue::ID_VALUE => [
                'id' => $taxon->getId(),
                'value' => $this->formatter->formatTaxon($taxon),
            ],
            ProductAttributeValue::ID => $taxon->getId(),
            default => $this->formatter->formatTaxon($taxon)
        };
    }

    private function getOptionValue(
        ProductOptionValueInterface $attributeValue,
        ?ProductAttributeValue $config,
    ): string|int|array {
        return match ($config) {
            ProductAttributeValue::ID_VALUE => [
                'id' => $attributeValue->getId(),
                'value' => $attributeValue->getValue(),
            ],
            ProductAttributeValue::ID => $attributeValue->getId(),
            default => $attributeValue->getValue()
        };
    }
}
