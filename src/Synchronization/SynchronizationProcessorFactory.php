<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Webmozart\Assert\Assert;

class SynchronizationProcessorFactory
{
    private array $processors;

    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function get(string $type): SynchronizationProcessorInterface
    {
        Assert::keyExists($this->processors, $type);

        return $this->processors[$type];
    }
}
