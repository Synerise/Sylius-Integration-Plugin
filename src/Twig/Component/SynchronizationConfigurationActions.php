<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

//use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
//use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;

#[AsTwigComponent]
class SynchronizationConfigurationActions
{
//    use HookableComponentTrait;
//    use DefaultActionTrait;

    /** @var array<int, SynchronizationConfigurationInterface> $configurations ; */
    #[ExposeInTemplate('configurations')]
    public array $configurations = [];

    /**
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $configurationRepository
     */
    public function __construct(
        private SynchronizationConfigurationRepositoryInterface $configurationRepository,
    ) {
    }

    #[PostMount]
    public function postMount(): void
    {
        /** @var array<int, SynchronizationConfigurationInterface> $configurations */
        $configurations = $this->configurationRepository->findAll();
        $this->configurations = $configurations;
    }
}
