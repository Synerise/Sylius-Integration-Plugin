<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;

class WorkspaceContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ContainerBagInterface $params,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Transform this workspace
     */
    public function getWorkspace(): WorkspaceInterface
    {
        return $this->sharedStorage->get('workspace');
    }

   /**
     * @Transform saved workspace
     */
    public function savedWorkspace(): WorkspaceInterface
    {
        $workspace = $this->sharedStorage->get('workspace');
        $this->entityManager->refresh($workspace);
        return $workspace;
    }

    /**
     * @Transform test api key
     */
    public function getTestApiKey(): string
    {
        return $this->params->get('synerise.test.api_key');
    }

    /**
     * @Transform test api guid
     */
    public function getTestApiGuid(): string
    {
        return $this->params->get('synerise.test.api_guid');
    }
}
