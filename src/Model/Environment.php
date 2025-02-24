<?php

namespace Synerise\SyliusIntegrationPlugin\Model;


use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Synerise\Sdk\Model\EnumTrait;
use Synerise\Sdk\Model\EnvironmentTrait;
use Synerise\Sdk\Model\EnvironmentInterface;

enum Environment: string implements EnvironmentInterface, TranslatableInterface
{
    use EnvironmentTrait;
    use EnumTrait;

    case Azure = self::AZURE_VALUE;

    case GCP = self::GCP_VALUE;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->label();
    }
}
