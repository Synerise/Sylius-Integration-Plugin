@channel_configuration
Feature: Channel Configuration Save
  In order to configure channel with a chosen workspace
  As an Administrator
  I want to configure selected channel with workspace

  Background:
    Given the store operates on a channel named "channelName" with hostname "example.com/main"
    And the store has a workspace named "workspaceName"
    And I am logged in as an administrator

  @javascript @e2e
  Scenario: Successfully save channel configuration
    Given the actual api requests will be sent
    And I am on "/admin/synerise/configuration/new"
    When I select "channelName" from "synerise_integration_channel_configuration_channel"
    And I select "workspaceName" from "synerise_integration_channel_configuration_workspace"
    And I press "Next"
    And I check "synerise_integration_channel_configuration_cookieDomainEnabled"
    And I check "synerise_integration_channel_configuration_trackingEnabled"
    And I wait for "#synerise_integration_channel_configuration_cookieDomain" element
    And I fill in "example.com" for "synerise_integration_channel_configuration_cookieDomain"
    And I press "Events tracking"
    And I click "[data-value='product.addToCart'] .remove" element
    And I click "[data-value='product.removeFromCart'] .remove" element
    And I click "#synerise_integration_channel_configuration_events-ts-control" element
    And I click "[data-value='product.removeFromCart']" element
    And I click "#synerise_integration_channel_configuration_events-ts-control" element
    And I check "synerise_integration_channel_configuration_snrsParamsEnabled"
    And I press "Configure"
    And I wait for ".alert" element
    Then the ".alert-success" element should contain "Channel configuration has been successfully created."
    And the saved channel configuration should exist in repository
    And the ".page-body .card" element should contain "channelName"
    And the ".page-body .card" element should contain "workspaceName"
    And the ".page-body #card-tracking tr:nth-child(1) td:last-child" element should contain "Yes"
    And the ".page-body #card-tracking tr:nth-child(2) td:last-child" element should contain "No"
    And the ".page-body #card-tracking tr:nth-child(3) td:last-child" element should contain "No"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should not contain "product.addToCart"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "product.removeFromCart"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "product.addReview"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "cart.status"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "client.login"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "client.logout"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "client.register"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "profile.update"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "transaction.charge"
    And the ".page-body #card-events tr:nth-child(1) td:last-child" element should contain "product.update"
    And the ".page-body #card-events tr:nth-child(2) td:last-child" element should contain "Yes"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "product.addToCart"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "product.removeFromCart"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "product.addReview"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "cart.status"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "client.login"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "client.logout"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "client.register"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "profile.update"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "transaction.charge"
    And the ".page-body #card-events tr:nth-child(3) td:last-child" element should contain "product.update"

  @javascript @e2e
  Scenario: Filled inputs on edit view after successfull save
    Given the actual api requests will be sent
    And I am on "/admin/synerise/configuration/new"
    When I select "channelName" from "synerise_integration_channel_configuration_channel"
    And I select "workspaceName" from "synerise_integration_channel_configuration_workspace"
    And I press "Next"
    And I check "synerise_integration_channel_configuration_cookieDomainEnabled"
    And I check "synerise_integration_channel_configuration_trackingEnabled"
    And I wait for "#synerise_integration_channel_configuration_cookieDomain" element
    And I fill in "example.com" for "synerise_integration_channel_configuration_cookieDomain"
    And I press "Events tracking"
    And I click "[data-value='product.addToCart'] .remove" element
    And I click "[data-value='product.removeFromCart'] .remove" element
    And I click "#synerise_integration_channel_configuration_events-ts-control" element
    And I click "[data-value='product.removeFromCart']" element
    And I click "#synerise_integration_channel_configuration_events-ts-control" element
    And I check "synerise_integration_channel_configuration_snrsParamsEnabled"
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='product.addToCart'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='product.removeFromCart'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='product.addReview'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='cart.status'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='client.login'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='client.logout'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='client.register'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='profile.update'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='transaction.charge'] .remove" element
    And I click "[data-card='v-steps-events'] .field:nth-child(3) [data-value='product.update'] .remove" element
    And I press "Configure"
    And I wait for ".alert" element
    And I click ".btn-primary" element
    Then the "#synerise_integration_channel_configuration_channel" element should contain "channelName"
    And the "#synerise_integration_channel_configuration_workspace" element should contain "workspaceName"
    And I press "Page tracking"
    And the "synerise_integration_channel_configuration_cookieDomainEnabled" checkbox should be checked
    And the "synerise_integration_channel_configuration_trackingEnabled" checkbox should be checked
    And the "synerise_integration_channel_configuration_cookieDomain" field should contain "example.com"
    And I press "Events tracking"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should not contain "product.addToCart"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "product.removeFromCart"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "product.addReview"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "cart.status"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "client.login"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "client.logout"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "client.register"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "profile.update"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "transaction.charge"
    And the "[data-card='v-steps-events'] .field:nth-child(1) .ts-control" element should contain "product.update"
    And the "synerise_integration_channel_configuration_snrsParamsEnabled" checkbox should be checked
    And I should not see a "[data-card='v-steps-events'] .field:nth-child(3) .ts-control .badge" element

  @javascript @default
  Scenario: Error on save channel configuration with invalid custom cookie domain
    Given I am on "/admin/synerise/configuration/new"
    When I select "channelName" from "synerise_integration_channel_configuration_channel"
    And I select "workspaceName" from "synerise_integration_channel_configuration_workspace"
    And I press "Next"
    And I check "synerise_integration_channel_configuration_trackingEnabled"
    And I check "synerise_integration_channel_configuration_cookieDomainEnabled"
    And I wait for "#synerise_integration_channel_configuration_cookieDomain" element
    And I fill in "invalid-example.com" for "synerise_integration_channel_configuration_cookieDomain"
    And I press "Events tracking"
    And I press "Configure"
    And I wait for ".alert" element
    Then the ".alert-danger" element should contain "This form contains errors."
    And I should see a "#synerise_integration_channel_configuration_cookieDomain.is-invalid" element
