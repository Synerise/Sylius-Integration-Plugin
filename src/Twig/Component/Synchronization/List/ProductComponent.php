<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class ProductComponent
{
    use HookableComponentTrait;

    public ?ChannelInterface $channel = null;

    #[ExposeInTemplate]
    public ?string $type = 'Product';

    #[ExposeInTemplate]
    public int $sent = 0;

    #[ExposeInTemplate]
    public int $total = 0;

    public function __construct(
        private EntityRepository $productRepository,
        private EntityRepository $productStatusRepository
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->sent = $this->productStatusRepository->count(['channel' => $this->channel]);
        $this->total = (int) $this->productRepository->createQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $this->channel)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
