<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ProductAttributeValue: string implements TranslatableInterface
{
    case ID_VALUE = 'id_value';

    case VALUE = 'value';

    case ID = 'id';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->getLabel(), locale: $locale);
    }

    public function getLabel(): string
    {
        return sprintf('synerise_integration.ui.%s', $this->value);
    }
}
