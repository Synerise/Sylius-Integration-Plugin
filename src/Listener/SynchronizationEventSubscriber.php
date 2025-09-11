<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Resource\Symfony\Routing\RedirectHandler;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class SynchronizationEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param SynchronizationConfigurationRepositoryInterface<SynchronizationConfigurationInterface> $repository
     */
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $syneriseLogger,
        private RequestStack $requestStack,
        private RedirectHandler $redirectHandler,
        private SynchronizationConfigurationRepositoryInterface $repository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'synerise_integration.synchronization.initialize_create' => 'onInitializeCreate',
            'synerise_integration.synchronization.pre_create' => 'onPreCreate',
            'synerise_integration.synchronization.post_create' => 'onPostCreate',
        ];
    }

    public function onInitializeCreate(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            throw new \RuntimeException('No request available in request stack');
        }

        $synchronizationConfiguration = $this->getSynchronizationConfigurationOr404();

        $request->attributes->set('synchronizationConfiguration', $synchronizationConfiguration);
    }

    public function onPreCreate(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();
        $synchronizationConfiguration = $this->getSynchronizationConfigurationOr404();

        $synchronization->setStatus(SynchronizationStatus::Created);
        $synchronization->setChannel($synchronizationConfiguration->getChannel());
        $synchronization->setConfigurationSnapshot(json_encode($synchronizationConfiguration) ?: null);
        $synchronization->setSent(0);
    }

    public function onPostCreate(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();
        $configuration = $this->getConfiguration();
        $synchronizationConfiguration = $this->getSynchronizationConfigurationOr404();

        try {
            Assert::notNull($synchronization->getId());
            Assert::notNull($synchronization->getType());

            $this->messageBus->dispatch(new SyncStartMessage($synchronization->getId(), $synchronization->getType()->value));

            $response = $this->redirectHandler->redirectToRoute(
                $configuration,
                'synerise_integration_admin_synchronization_configuration_show',
                ['id' => $synchronizationConfiguration->getId()]
            );

            $event->setResponse($response);
        } catch (ExceptionInterface $e) {
            $this->syneriseLogger->error($e);
        }
    }

    private function getSynchronizationConfigurationOr404(): SynchronizationConfigurationInterface
    {
        $request = $this->requestStack->getCurrentRequest();

        $configurationId = $request?->get('configurationId');

        /** @var SynchronizationConfigurationInterface|null $synchronizationConfiguration */
        $synchronizationConfiguration = $this->repository->find($configurationId);

        if ($synchronizationConfiguration == null) {
            throw new NotFoundHttpException(sprintf('The synchronization configuration with id: "%s" has not been found', is_scalar($configurationId) ? (string) $configurationId : 'invalid'));
        }

        return $synchronizationConfiguration;
    }

    private function getConfiguration(): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        $configuration = $request?->attributes->get('_sylius');

        if ($configuration == null) {
            throw new NotFoundHttpException('The configuration has not been found');
        }

        return $configuration;
    }
}
