<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component\Synchronization\List;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;

abstract class AbstractComponent
{
    use HookableComponentTrait;

    public ?ChannelInterface $channel = null;

    #[ExposeInTemplate]
    public string $type;

    #[ExposeInTemplate]
    public int $sent = 0;

    #[ExposeInTemplate]
    public int $total = 0;

    public function __construct(
        protected EntityRepository $entityRepository,
        protected EntityRepository $statusRepository
    ) {
    }

    abstract public function postMount(): void;
}
