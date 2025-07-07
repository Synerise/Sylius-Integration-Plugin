<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Symfony\Component\Uid\Uuid;
use Synerise\Sdk\Helper\UuidGenerator as UuidGeneratorInterface;

class UuidGenerator implements UuidGeneratorInterface
{
    public function uuid5(string $string): string
    {
        return Uuid::v5(new Uuid(Uuid::NAMESPACE_DNS), $string)->toRfc4122();
    }
}
