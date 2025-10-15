<?php

declare(strict_types=1);

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Synerise\SyliusIntegrationPlugin\Entity\WorkspaceInterface;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\HandlerQueueFactory;
use Webmozart\Assert\Assert;

final class WorkspaceContext extends MinkContext implements Context
{
    /**
     * @Given I am on the workspace creation page
     */
    public function iAmOnTheWorkspaceCreationPage(): void
    {
        $this->visitPath('/admin/synerise/workspace/new');
    }

    /**
     * @When /^I am on the edit page of (this workspace)$/
     */
    public function iAmOnTheEditPageForWorkspace(WorkspaceInterface $workspace): void
    {
        $this->visitPath(sprintf('/admin/synerise/workspace/%d/edit', $workspace->getId()));
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
     * @Then /^I should be on show page for (this workspace)$/
     */
    public function iShouldBeOnTheWorkspaceEditPage(WorkspaceInterface $workspace): void
    {
        Assert::contains($this->getSession()->getCurrentUrl(), sprintf('/admin/synerise/workspace/%d', $workspace->getId()));
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

    /**
     * @Given check-permission will be mocked with a success response
     */
    public function checkPermissionWillBeMockedWithSuccess(): void
    {
        $this->getSession()->setCookie(
            HandlerQueueFactory::MOCK_HANDLER_QUEUE_COOKIE,
            json_encode(['api_key_check_permission_success'])
        );
    }
}
