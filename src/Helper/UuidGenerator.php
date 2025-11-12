<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Ramsey\Uuid\Uuid;
use Synerise\Sdk\Helper\UuidGenerator as UuidGeneratorInterface;

class UuidGenerator implements UuidGeneratorInterface
{
    public function uuid5(string $string): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_DNS, $string)->toString();
    }
}
