<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Synerise\Sdk\Model\EnumTrait;

enum Mode: string implements TranslatableInterface
{
    use EnumTrait;

    case Live = 'live';
    case Scheduled = 'scheduled';

    public const LABEL = [
        'live' => 'synerise_integration.workspace.mode.live',
        'scheduled' => 'synerise_integration.workspace.mode.scheduled',
    ];

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->label(), locale: $locale);
    }

    public function label(): string
    {
        return self::LABEL[$this->value];
    }
}
