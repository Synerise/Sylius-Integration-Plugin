<?php

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Resource\Model\ResourceInterface;
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
     * @return void
     */
    public function process(ResourceInterface $resource)
    {
        Assert::implementsInterface($resource, ProductInterface::class);
    }
}
