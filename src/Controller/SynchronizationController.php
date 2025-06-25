<?php

namespace Synerise\SyliusIntegrationPlugin\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationConfigurationRepository;
use Webmozart\Assert\Assert;

class SynchronizationController extends ResourceController
{

    public function createAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var string $configurationId */
        $configurationId = $configuration->getRequest()->get('configurationId');

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);

        /**
         * @var Synchronization $newResource
         */
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);
        $synchronizationConfiguration = $this->findConfigurationOr404($configurationId);

        $form = $this->resourceFormFactory->create($configuration, $newResource);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Synchronization $newResource
             */
            $newResource = $form->getData();
            $newResource->setStatus(SynchronizationStatus::Created);
            $newResource->setChannel($synchronizationConfiguration->getChannel());
            $newResource->setConfigurationSnapshot(json_encode($synchronizationConfiguration) ?: null);
            $newResource->setSent(0);

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                $eventResponse = $event->getResponse();
                if (null !== $eventResponse) {
                    return $eventResponse;
                }

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            if ($configuration->hasStateMachine()) {
                $stateMachine = $this->getStateMachine();
                $stateMachine->apply($configuration, $newResource);
            }

            $this->repository->add($newResource);

            if ($configuration->isHtmlRequest()) {
                $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);
            }

            $postEvent = $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->createRestView($configuration, $newResource, Response::HTTP_CREATED);
            }

            $postEventResponse = $postEvent->getResponse();
            if (null !== $postEventResponse) {
                return $postEventResponse;
            }

            return $this->redirectHandler->redirectToRoute($configuration, 'synerise_integration_admin_synchronization_configuration_show', ['id' => $synchronizationConfiguration->getId()]);
        }

        if ($request->isMethod('POST') && $form->isSubmitted() && !$form->isValid()) {
            $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->createRestView($configuration, $form, Response::HTTP_BAD_REQUEST);
        }

        $initializeEvent = $this->eventDispatcher->dispatchInitializeEvent(ResourceActions::CREATE, $configuration, $newResource);
        $initializeEventResponse = $initializeEvent->getResponse();
        if (null !== $initializeEventResponse) {
            return $initializeEventResponse;
        }

        /** @var string $template */
        $template = $configuration->getTemplate(ResourceActions::CREATE . '.html');

        return $this->render($template, [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resource' => $newResource,
            'synchronizationConfiguration' => $synchronizationConfiguration,
            $this->metadata->getName() => $newResource,
            'form' => $form->createView(),
        ], null, $responseCode ?? Response::HTTP_OK);
    }

    private function findConfigurationOr404(string $configurationId): SynchronizationConfigurationInterface
    {
        /** @var SynchronizationConfigurationInterface|null $synchronizationConfiguration */
        $synchronizationConfiguration = $this->getSynchronizationConfigurationRepository()->find($configurationId);
        if ($synchronizationConfiguration == null) {
            throw new NotFoundHttpException(sprintf('The configuration with id: "%s" has not been found', $configurationId));
        }

        return $synchronizationConfiguration;
    }

    private function getSynchronizationConfigurationRepository(): SynchronizationConfigurationRepository
    {
        $repository = $this->get('synerise_integration.repository.synchronization_configuration');
        Assert::isInstanceOf($repository, SynchronizationConfigurationRepository::class);
        return $repository;
    }
}
