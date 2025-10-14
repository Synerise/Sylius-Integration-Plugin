@synerise_workspace
Feature: Workspace management
  In order to manage Synerise integration settings
  As an administrator
  I want to be able to save workspace configurations

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @mock-api
  Scenario: Successfully saving a new workspace
    Given check-permission will be mocked with a success response
    And I am on the workspace creation page
    When I fill in test api key
    And I select "basic" from "synerise_integration_workspace[authenticationMethod]"
    And I select "1" from "synerise_integration_workspace[keepAliveEnabled]"
    And I check "Log requests and responses"
    And I press "Create"
    Then I should see "GUID is required when using Basic authentication"
    And I fill in test api guid
    And I press "Create"
    Then I should see "Workspace has been successfully created"
    And logs should show 2 requests to "/uauth/api-key/permission-check"
    And logs should show 2 requests in total

  Scenario: Saving workspace with invalid API Key format
    Given I am on the workspace creation page
    When I fill in the following:
      | synerise_integration_workspace[apiKey] | invalid-key |
    And I press "Create"
    Then I should see "This value is not a valid UUID"
    And I should remain on the workspace creation page

  Scenario: Saving workspace with invalid API GUID format
    Given I am on the workspace creation page
    When I select "basic" from "synerise_integration_workspace[authenticationMethod]"
    And I press "Create"
    And I fill in the following:
      | synerise_integration_workspace[guid] | invalid-guid |
    And I press "Create"
    Then I should see "This value is not a valid GUID"
    And I should remain on the workspace creation page

  @mock-api
  Scenario: Successfully updating an existing workspace
    Given check-permission will be mocked with a success response
    And there is a workspace with test api key
    When I am on the edit page of this workspace
    And I change "synerise_integration_workspace[liveTimeout]" to "3"
    And I press "Update"
    Then I should be on show page for this workspace
    And I should see "Workspace has been successfully updated"
    And saved workspace should have live timeout 3
    And logs should show 2 requests to "/uauth/api-key/permission-check"
    And logs should show 2 requests in total
