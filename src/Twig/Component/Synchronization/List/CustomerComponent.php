<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Symfony\UX\TwigComponent\Attribute\PostMount;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;

class CustomerComponent extends AbstractComponent
{
    public string $type = SynchronizationDataType::CUSTOMER_LABEL;

    #[PostMount]
    public function postMount(): void
    {
        $this->sent = 0;
        $this->total = $this->entityRepository->count([]);
    }
}
