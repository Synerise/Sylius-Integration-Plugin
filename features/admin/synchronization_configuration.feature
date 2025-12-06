@synchronization_configuration
Feature: Synchronization Configuration
  In order to configure synchronization configuration with a chosen already configured channel
  As an Administrator
  I want to configure synchronization configuration with selected channel

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel
    And the store has a textarea product attribute "Description"
    And the store has a text product attribute "Brand"
    And the store has a integer product attribute "Pages"
    And I am logged in as an administrator

  @default
  Scenario: Successfully save synchronization configuration with default values and new synerise catalog
    Given I am on "/admin/synerise/synchronization_configuration/new"
    And api response will be mocked with:
      | get_catalogs_bags_success |
      | post_catalogs_bags_success |
    When I press "Create"
    Then logs should show 2 request in total
    And logs should show 2 requests to "/catalogs/bags"
    And I should see "Synchronization configuration has been successfully created."
    And the saved synchronization configuration should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "1234"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "#card-config tr:nth-child(4) td:last-child" element should contain "Value"

  @javascript @default
  Scenario: Successfully save synchronization configuration with selected values and existing synerise catalog
    Given I am on "/admin/synerise/synchronization_configuration/new"
    And there is a synerise catalog for this channel
    And api response will be mocked with:
      | get_catalogs_bags_success |
    When I click "#synerise_integration_synchronization_configuration_productAttributes-ts-control" element
    And I click "#synerise_integration_synchronization_configuration_productAttributes option:nth-child(2)" element
    And I click "#synerise_integration_synchronization_configuration_productAttributes option:nth-child(1)" element
    And I click "#synerise_integration_synchronization_configuration_productAttributes-ts-control" element
    And I select "id" from "synerise_integration_synchronization_configuration[productAttributeValue]"
    And I press "Create"
    Then logs should show 1 request in total
    And logs should show 1 requests to "/catalogs/bags"
    And I should see "Synchronization configuration has been successfully created."
    And the saved synchronization configuration should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "123"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "#card-config tr:nth-child(3) td:last-child" element should contain "Description"
    And the "#card-config tr:nth-child(3) td:last-child" element should contain "Brand"
    And the "#card-config tr:nth-child(4) td:last-child" element should contain "Id"
