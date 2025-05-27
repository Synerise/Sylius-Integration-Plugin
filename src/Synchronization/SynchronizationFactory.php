<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Webmozart\Assert\Assert;

class SynchronizationFactory
{
    private array $synchronizationProcessors;

    public function __construct(array $synchronizationProcessors)
    {
        $this->synchronizationProcessors = $synchronizationProcessors;
    }

    public function get(string $type): SynchronizationProcessorInterface
    {
        Assert::keyExists($this->synchronizationProcessors, $type);

        return $this->synchronizationProcessors[$type];
    }
}
