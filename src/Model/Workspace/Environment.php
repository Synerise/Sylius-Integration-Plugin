<?php

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;


use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Synerise\Sdk\Model\EnumTrait;
use Synerise\Sdk\Model\EnvironmentInterface;
use Synerise\Sdk\Model\EnvironmentTrait;

enum Environment: string implements EnvironmentInterface, TranslatableInterface
{
    use EnvironmentTrait;
    use EnumTrait;

    case Azure = self::AZURE_VALUE;

    case AzureUs = self::AZURE_US_VALUE;

    case GCP = self::GCP_VALUE;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->label();
    }
}
