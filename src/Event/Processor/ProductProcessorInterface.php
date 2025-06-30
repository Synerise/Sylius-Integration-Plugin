<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;


use Sylius\Component\Core\Model\ProductInterface;

interface ProductProcessorInterface
{
    public function process(ProductInterface $product): void;
}
