<?php

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Resource\Model\ResourceInterface;

interface ResourceProcessorInterface
{
    public function supports(string $resourceType): bool;
    public function process(ResourceInterface $resource): Parsable;
}
