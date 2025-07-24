<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Resource\Symfony\Routing\RedirectHandler;
// use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;
use Webmozart\Assert\Assert;

final readonly class SynchronizationPostCreateListener
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private LoggerInterface $syneriseLogger,
        private RequestStack $requestStack,
        private RedirectHandler $redirectHandler,
        private SynchronizationConfigurationRepository $repository,
    ) {
    }

    public function __invoke(ResourceControllerEvent $event): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $event->getSubject();
        $request = $this->requestStack->getCurrentRequest();
        $configuration = $request->attributes->get('_sylius');
        $configurationId = $request->get('configurationId');
        $synchronizationConfiguration = $this->repository->find($configurationId);

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
}
