@synchronization_configuration
Feature: Synchronization Configuration
  In order to configure synchronization with a chosen already configured channel
  As an Administrator
  I want to configure synchronization with selected channel

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel
    And I am logged in as an administrator

  @default
  Scenario: Successfully save synchronization configuration with default values
    Given I am on "/admin/synerise/synchronization_configuration/new"
    And api response will be mocked with:
        | catalogs_bags_success |
    And I press "Create"
    Then the ".alert-success" element should contain "Channel configuration has been successfully created."
    And the saved synchronization configuration should exist in repository
