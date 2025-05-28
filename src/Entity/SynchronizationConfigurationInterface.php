<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SynchronizationConfigurationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getDataTypes(): array;

    public function setDataTypes(?array $dataTypes): void;

    public function getProductAttributes(): array;

    public function setProductAttributes(?array $productAttributes): void;

    public function getCatalogId(): ?int;

    public function setCatalogId(?int $catalogId): void;

    public function getProductAttributeValue(): ?ProductAttributeValue;

    public function setProductAttributeValue(?ProductAttributeValue $productAttributeValue): void;
}
