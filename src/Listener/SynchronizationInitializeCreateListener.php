<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;

final readonly class SynchronizationInitializeCreateListener
{
    public function __construct(
        private RequestStack $requestStack,
        private SynchronizationConfigurationRepository $repository,
    ) {
    }

    public function __invoke(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $configurationId = $request->get('configurationId');
        $synchronizationConfiguration = $this->repository->find($configurationId);

        if ($synchronizationConfiguration == null) {
            throw new NotFoundHttpException(sprintf('The configuration with id: "%s" has not been found', $configurationId));
        }

        $request->attributes->set('synchronizationConfiguration', $synchronizationConfiguration);
    }
}
