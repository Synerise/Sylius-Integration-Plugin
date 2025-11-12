<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin;

use Doctrine\DBAL\Types\Type;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyneriseSyliusIntegrationPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function boot(): void
    {
        parent::boot();

        if (!Type::hasType('uuid_binary')) {
            Type::addType('uuid_binary', UuidBinaryType::class);
        }

        $em = $this->container->get('doctrine')->getManager();
        $platform = $em->getConnection()->getDatabasePlatform();

        if (!$platform->hasDoctrineTypeMappingFor('uuid_binary')) {
            $platform->registerDoctrineTypeMapping('uuid_binary', 'binary');
        }
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
