<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;

final class WorkspaceContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @Given /^there is a workspace with (test api key)$/
     * @Given /^there is a workspace with (:apiKey)$/
     */
    public function thereIsAWorkspaceNamedWithApiKey(string $apiKey): void
    {
        $workspace = new Workspace();
        $workspace->setApiKey($apiKey);

        $this->entityManager->persist($workspace);
        $this->entityManager->flush();

        $this->sharedStorage->set('workspace', $workspace);
    }

    /**
     * @Given /^there is a workspace with (test api key) and request logging enabled$/
     * @Given /^there is a workspace with (:apiKey)and request logging enabled$/
     */
    public function thereIsAWorkspaceNamedWithApiKeyAndRequestLoggingEnabled(string $apiKey): void
    {
        $workspace = new Workspace();
        $workspace->setApiKey($apiKey);
        $workspace->setRequestLoggingEnabled(true);

        $this->entityManager->persist($workspace);
        $this->entityManager->flush();

        $this->sharedStorage->set('workspace', $workspace);
    }

}
