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
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;
use Webmozart\Assert\Assert;

final readonly class SynchronizationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $syneriseLogger,
        private RequestStack $requestStack,
        private RedirectHandler $redirectHandler,
        private SynchronizationConfigurationRepository $repository,
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

        $synchronizationConfiguration = $this->getSynchronizationConfiguration();
        if ($synchronizationConfiguration == null) {
            throw new NotFoundHttpException(sprintf('The configuration with id: "%s" has not been found', $synchronizationConfiguration));
        }

        $request->attributes->set('synchronizationConfiguration', $synchronizationConfiguration);
    }

    public function onPreCreate(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();
        $synchronizationConfiguration = $this->getSynchronizationConfiguration();

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
        $synchronizationConfiguration = $this->getSynchronizationConfiguration();

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

    private function getSynchronizationConfiguration(): ?object
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $configurationId = $request->get('configurationId');
        $synchronizationConfiguration = $this->repository->find($configurationId);

        return $synchronizationConfiguration;
    }

    private function getConfiguration()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $configuration = $request->attributes->get('_sylius');
        return $configuration;
    }
}
