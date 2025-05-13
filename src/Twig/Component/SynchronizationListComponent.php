<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class SynchronizationListComponent
{
    use HookableComponentTrait;

    public ?string $type = null;

    public $channel = null;

    #[ExposeInTemplate]
    public int $sent = 0;

    #[ExposeInTemplate]
    public int $total = 0;

    public function __construct(
        private EntityRepository $customerRepository,
        private EntityRepository $orderRepository,
        private EntityRepository $productRepository,
        private EntityRepository $customerStatusRepository,
        private EntityRepository $orderStatusRepository,
        private EntityRepository $productStatusRepository
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->countSent();
        $this->countTotal();
    }

    public function countSent(): void
    {
        switch ($this->type) {
            case 'Customer':
                $this->sent = $this->customerStatusRepository->count(['channel' => $this->channel]);
                break;
            case 'Order':
                $this->sent = $this->orderStatusRepository->count(['channel' => $this->channel]);
                break;
            case 'Product':

                $this->sent = $this->productStatusRepository->count(['channel' => $this->channel]);
                break;
        }
    }

    public function countTotal(): void
    {
        switch ($this->type) {
            case 'Customer':
                $this->total = $this->customerRepository->count([]);
                break;
            case 'Order':
                $this->total = $this->orderRepository->count(['channel' => $this->channel]);
                break;
            case 'Product':

                $this->total = $this->productRepository->createQueryBuilder('o')
                    ->select('COUNT(o)')
                    ->andWhere(':channel MEMBER OF o.channels')
                    ->setParameter('channel', $this->channel)
                    ->getQuery()
                    ->getSingleScalarResult()
                ;
                break;
        }
    }
}
