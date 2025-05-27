<?php

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Resource\Model\ResourceInterface;
use Synerise\Api\Catalogs\Models\AddItem;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;
use Webmozart\Assert\Assert;

class ProductResourceProcessor implements ResourceProcessorInterface
{
    public function supports(string $resourceType): bool
    {
        return $resourceType == SynchronizationDataType::Product;
    }

    /**
     * @param ProductInterface $resource
     * @return AddItem
     */
    public function process(ResourceInterface $resource): Parsable
    {
        Assert::implementsInterface($resource, ProductInterface::class);
    }
}
