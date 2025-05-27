<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;

class SynchronizationConfiguration implements SynchronizationConfigurationInterface, \JsonSerializable
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?array $dataTypes = null;

    private ?array $productAttributes = null;

    private ?int $catalogId = null;

    private ?ProductAttributeValue $productAttributeValue = null;

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

    public function getProductAttributes(): array
    {
        return $this->productAttributes ?: [];
    }

    public function setProductAttributes(?array $productAttributes): void
    {
        $this->productAttributes = $productAttributes;
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
