<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SynchronizationConfigurationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function getProductAttributes(): Collection;

    public function addProductAttribute(ProductAttribute $productAttribute): void;

    public function removeProductAttribute(ProductAttribute $productAttribute): void;

    /**
     * @param array<ProductAttribute> $productAttributes
     */
    public function setProductAttributes(array $productAttributes): void;

    public function getCatalogId(): ?int;

    public function setCatalogId(?int $catalogId): void;

    public function getProductAttributeValue(): ?ProductAttributeValue;

    public function setProductAttributeValue(?ProductAttributeValue $productAttributeValue): void;
}
