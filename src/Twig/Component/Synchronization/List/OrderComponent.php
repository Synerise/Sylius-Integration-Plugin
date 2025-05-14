<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class OrderComponent
{
    use HookableComponentTrait;

    public ?ChannelInterface $channel = null;

    #[ExposeInTemplate]
    public ?string $type = 'Order';

    #[ExposeInTemplate]
    public int $sent = 0;

    #[ExposeInTemplate]
    public int $total = 0;

    public function __construct(
        private EntityRepository $orderRepository,
        private EntityRepository $orderStatusRepository
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->sent = $this->orderStatusRepository->count(['channel' => $this->channel]);
        $this->total = $this->orderRepository->count(['channel' => $this->channel]);
    }
}
