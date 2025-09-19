<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;
use Webmozart\Assert\Assert;

final class WorkspaceContext extends MinkContext implements Context
{
    /**
     * @Given I am on the workspace creation page
     */
    public function iAmOnTheWorkspaceCreationPage(): void
    {
        $this->visit('/admin/synerise/workspace/new');
    }

    /**
     * @When /^I am on the edit page of (this workspace)$/
     */
    public function iAmOnTheEditPageForWorkspace(WorkspaceInterface $workspace): void
    {
        $this->visit(sprintf('/admin/synerise/workspace/%d/edit', $workspace->getId()));
    }

    /**
     * @When I change :field to :value
     */
    public function iChangeFieldTo(string $field, string $value): void
    {
        $this->fillField($field, $value);
    }

    /**
     * @Then I should remain on the workspace creation page
     */
    public function iShouldRemainOnTheWorkspaceCreationPage(): void
    {
        Assert::contains($this->getSession()->getCurrentUrl(), '/admin/synerise/workspace/new');
    }

    /**
     * @Then /^(saved workspace) should have live timeout (\d+)$/
     */
    public function savedWorkspaceShouldHaveLiveTimeout(WorkspaceInterface $workspace, string $timeout): void
    {
        Assert::same($workspace->getLiveTimeout(), (float) $timeout);
    }

    /**
     * @When /^I fill in (test api key)$/
     */
    public function iFillInTestApiKey(string $apiKey): void
    {
        $this->fillField('synerise_integration_workspace_apiKey', $apiKey);
    }

    /**
     * @When /^I fill in (test api guid)$/
     */
    public function iFillInTestApiGuid(string $apiGuid): void
    {
        $this->fillField('synerise_integration_workspace_guid', $apiGuid);
    }
}
