<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Product\Model\ProductAttribute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SynchronizationConfiguration implements SynchronizationConfigurationInterface, \JsonSerializable
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?array $dataTypes = null;

    /** @var Collection<int, ProductAttribute> */
    private Collection $productAttributes;

    private ?int $catalogId = null;

    private ?ProductAttributeValue $productAttributeValue = null;

    public function __construct()
    {
        $this->productAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getDataTypes(): array
    {
        return $this->dataTypes ?: [];
    }

    public function setDataTypes(?array $dataTypes): void
    {
        $this->dataTypes = $dataTypes;
    }

    /**
     * @return Collection<int, ProductAttribute>
     */
    public function getProductAttributes(): Collection
    {
        return $this->productAttributes;
    }

    public function addProductAttribute(ProductAttribute $productAttribute): void
    {
        if (!$this->productAttributes->contains($productAttribute)) {
            $this->productAttributes->add($productAttribute);
        }
    }

    public function removeProductAttribute(ProductAttribute $productAttribute): void
    {
        if ($this->productAttributes->contains($productAttribute)) {
            $this->productAttributes->removeElement($productAttribute);
        }
    }

    public function getCatalogId(): ?int
    {
        return $this->catalogId;
    }

    public function setCatalogId(?int $catalogId): void
    {
        $this->catalogId = $catalogId;
    }


    public function getProductAttributeValue(): ?ProductAttributeValue
    {
        return $this->productAttributeValue;
    }

    public function setProductAttributeValue(?ProductAttributeValue $productAttributeValue): void
    {
        $this->productAttributeValue = $productAttributeValue;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'channel' => $this->channel?->getCode(),
            'dataTypes' => $this->dataTypes,
        ];

    }
}
