<?php

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Synerise\Sdk\Model\AuthenticationMethodInterface;
use Synerise\Sdk\Model\EnumTrait;

enum AuthenticationMethod: string implements AuthenticationMethodInterface, TranslatableInterface
{
    use EnumTrait;

    case Bearer = self::BEARER_VALUE;

    case Basic = self::BASIC_VALUE;

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $this->label();
    }
}
