<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsTwigComponent]
class CustomerComponent
{
    use HookableComponentTrait;

    public ?ChannelInterface $channel = null;

    #[ExposeInTemplate]
    public ?string $type = 'Customer';

    #[ExposeInTemplate]
    public int $sent = 0;

    #[ExposeInTemplate]
    public int $total = 0;

    public function __construct(
        private EntityRepository $customerRepository,
        private EntityRepository $customerStatusRepository
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->sent = $this->customerStatusRepository->count(['channel' => $this->channel]);
        $this->total = $this->customerRepository->count([]);
    }
}
