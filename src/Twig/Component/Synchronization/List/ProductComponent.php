<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Symfony\UX\TwigComponent\Attribute\PostMount;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationDataType;

class ProductComponent extends AbstractComponent
{
    public string $type = SynchronizationDataType::PRODUCT_LABEL;

    #[PostMount]
    public function postMount(): void
    {
        $this->sent = 0;
        $this->total = (int) $this->entityRepository->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $this->channel)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
