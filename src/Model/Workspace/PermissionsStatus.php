<?php

namespace Synerise\SyliusIntegrationPlugin\Model\Workspace;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Synerise\Sdk\Model\EnumTrait;

enum PermissionsStatus: string implements TranslatableInterface
{
    use EnumTrait;

    case FullAccess = 'full_access';

    case PartialAccess = 'partial_access';

    case NoAccess = 'no_access';

    public const LABEL = [
        'full_access' => 'synerise_integration..workspace.permissions_status.full_access',
        'partial_access' => 'synerise_integration..workspace.permissions_status.partial_access',
        'no_access' => 'synerise_integration..workspace.permissions_status.no_access',
    ];

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->label(), locale: $locale);
    }

    public function label(): string
    {
        return 'synerise_integration.workspace.permissions_status.'.$this->value;
    }
}
