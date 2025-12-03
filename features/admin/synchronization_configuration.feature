@synchronization_configuration
Feature: Synchronization Configuration
  In order to configure synchronization with a chosen already configured channel
  As an Administrator
  I want to configure synchronization with selected channel

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And the store has a textarea product attribute "Description"
    And the store has a text product attribute "Brand"
    And the store has a integer product attribute "Pages"
    And the store has customer "John Doe" with email "john.doe@mail.com"
    And this workspace is assigned to this channel
    And I am logged in as an administrator

  @defaultx
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

  @javascript @defaultx
  Scenario: Successfully save synchronization configuration with not default values and existing synerise catalog
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

  @javascript @default
  Scenario: Successfully save customer synchronization from listing
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1       |
        | productAttributeValue | value   |
    And api response will be mocked with:
        | accepted  |
    And I am on "/admin/synerise/synchronization_configuration/"
    When I follow "New sync"
    Then the url should match "/admin/synerise/synchronization_configuration/\d+/synchronization/new"
    When I select "customer" from "synerise_integration_synchronization[type]"
    When I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    When I fill in "2025-02-01" for "synerise_integration_synchronization[untilWhen]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    And the url should match "/admin/synerise/synchronization_configuration/\d+"
    And the saved synchronization configuration should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "1"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(1)" element should contain "Customer"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(2)" element should contain "0"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(4)" element should contain current date
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(5)" element should contain "CREATED"
    And I should have 1 message in the queue
    When I process 1 message
    Then I should have 0 message in the queue
    And logs should show 1 request in total

  @javascript @default
  Scenario: Successfully save product synchronization from listing
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1       |
        | productAttributeValue | value   |
    And api response will be mocked with:
        | accepted  |
    And I am on "/admin/synerise/synchronization_configuration/"
    When I follow "New sync"
    Then the url should match "/admin/synerise/synchronization_configuration/\d+/synchronization/new"
    When I select "product" from "synerise_integration_synchronization[type]"
    When I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    When I fill in "2025-02-01" for "synerise_integration_synchronization[untilWhen]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    And the url should match "/admin/synerise/synchronization_configuration/\d+"
    And the saved synchronization configuration should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "1"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(1)" element should contain "Product"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(2)" element should contain "0"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(4)" element should contain current date
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(5)" element should contain "CREATED"
    And I should have 1 message in the queue
    When I process 1 message
    Then I should have 0 message in the queue
    And logs should show 1 request in total
