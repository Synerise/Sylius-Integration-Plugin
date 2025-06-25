<?php

namespace Synerise\SyliusIntegrationPlugin\Twig\Component;

use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfiguration;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;

#[AsTwigComponent]
class SynchronizationConfigurationActions
{
    use HookableComponentTrait;
    use DefaultActionTrait;

    /**
     * @var array<int, SynchronizationConfiguration> $configurations ;
     */
    #[ExposeInTemplate('configurations')]
    public array $configurations = [];

    public function __construct(
        private SynchronizationConfigurationRepository $configurationRepository
    )
    {
    }

    #[PostMount]
    public function postMount(): void
    {
        /** @var array<int, SynchronizationConfiguration> $configurations */
        $configurations = $this->configurationRepository->findAll();
        $this->configurations = $configurations;
    }
}
