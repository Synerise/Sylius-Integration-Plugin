<?php

namespace Synerise\SyliusIntegrationPlugin\Helper;

use Symfony\Component\Uid\Uuid;
use Synerise\Sdk\Helper\UuidGenerator as UuidGeneratorInterface;

class UuidGenerator implements UuidGeneratorInterface
{
    public function uuid5(string $string): string
    {
        return Uuid::v5(new Uuid($string), Uuid::NAMESPACE_DNS)->toRfc4122();
    }
}
