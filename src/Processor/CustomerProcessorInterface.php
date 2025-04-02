<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerProcessorInterface
{
    public function process(CustomerInterface $customer, string $action): void;
}
