<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum SynchronizationStatus: string implements TranslatableInterface
{
    case Created = 'created';
    case Processing = 'processing';
    case Ended = 'ended';
    case Cancelled = 'cancelled';

    public const CREATED_LABEL = 'synerise_integration.synchronization.status.created';

    public const PROCESSING_LABEL = 'synerise_integration.synchronization.status.processing';

    public const ENDED_LABEL = 'synerise_integration.synchronization.status.ended';

    public const CANCELLED_LABEL = 'synerise_integration.synchronization.status.cancelled';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getLabel(), locale: $locale);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => self::CREATED_LABEL,
            self::Processing => self::PROCESSING_LABEL,
            self::Ended => self::ENDED_LABEL,
            self::Cancelled => self::CANCELLED_LABEL,
        };
    }
}
