<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum SynchronizationDataType: string implements TranslatableInterface
{
    case Customer = 'customer';

    case Product = 'product';

    case Order = 'order';

    public const CUSTOMER_LABEL = 'synerise_integration.synchronization.customer';

    public const ORDER_LABEL = 'synerise_integration.synchronization.order';

    public const PRODUCT_LABEL = 'synerise_integration.synchronization.product';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getLabel(), locale: $locale);
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Customer  => self::CUSTOMER_LABEL,
            self::Product => self::PRODUCT_LABEL,
            self::Order  => self::ORDER_LABEL,
        };
    }
}
