<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Workspace;
use Webmozart\Assert\Assert;

final class WorkspaceContext extends MinkContext implements Context
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContainerBagInterface $params
    ) {
    }

    /**
     * @Given I am on the workspace creation page
     */
    public function iAmOnTheWorkspaceCreationPage(): void
    {
        $this->visit('/admin/synerise/workspace/new');
    }

    /**
     * @Given there is a workspace with test API key
     */
    public function thereIsAWorkspaceNamedWithApiKey(): void
    {
        $apiKey = $this->params->get('synerise.test.api_key');

        $workspace = new Workspace();
        $workspace->setApiKey($apiKey);

        $this->entityManager->persist($workspace);
        $this->entityManager->flush();
    }

    /**
     * @When I am on the edit page for test API key
     */
    public function iAmOnTheEditPageForWorkspace(): void
    {
        $apiKey = $this->params->get('synerise.test.api_key');
        $workspace = $this->findWorkspaceByApiKey($apiKey);
        $this->visit(sprintf('/admin/synerise/workspace/%d/edit', $workspace->getId()));
    }

    /**
     * @When I change :field to :value
     */
    public function iChangeFieldTo(string $field, string $value): void
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * @Then I should remain on the workspace creation page
     */
    public function iShouldRemainOnTheWorkspaceCreationPage(): void
    {
        Assert::contains($this->getSession()->getCurrentUrl(), '/admin/synerise/workspace/new');
    }

    /**
     * @Then the workspace should have live timeout :timeout
     */
    public function theWorkspaceShouldHaveLiveTimeout(string $timeout): void
    {
        $apiKey = $this->params->get('synerise.test.api_key');
        $workspace = $this->findWorkspaceByApiKey($apiKey);
        Assert::same($workspace->getLiveTimeout(), (float) $timeout);
    }

    /**
     * @When /^I fill in test api key$/
     */
    public function iFillInTestApiKey(): void
    {
        $apiKey = $this->params->get('synerise.test.api_key');
        $this->fillField('synerise_integration_workspace_apiKey', $apiKey);
    }

    /**
     * @When /^I fill in test api guid$/
     */
    public function iFillInTestApiGuid(): void
    {
        $apiKey = $this->params->get('synerise.test.api_guid');
        $this->fillField('synerise_integration_workspace_guid', $apiKey);
    }

    private function findWorkspaceByApiKey(string $apiKey): Workspace
    {
        $workspace = $this->entityManager->getRepository(Workspace::class)->findOneBy(['apiKey' => $apiKey]);
        $this->entityManager->refresh($workspace);
        Assert::notNull($workspace, sprintf('Workspace with api key "%s" not found', $apiKey));

        return $workspace;
    }

}
