<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\CustomerInterface;

interface CustomerProcessorInterface
{
    public function process(CustomerInterface $customer): void;
}
