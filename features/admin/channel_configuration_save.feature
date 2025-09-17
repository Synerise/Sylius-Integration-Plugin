@managing_channel_configurations
Feature: Channel Configuration Save
    In order to configure channel with a chosen workspace
    As an Administrator
    I want to configure selected channel with workspace

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    Scenario: Successfully save channel configuration
        Given I am on the channel configuration creation page
        When I select "channel" from "synerise_integration_channel_configuration_channel"
        And I select "workspace" from "synerise_integration_channel_configuration_workspace"
        And I click "Next" button
        And I set "synerise_integration_channel_configuration_trackingEnabled" to enabled
        And I set "synerise_integration_channel_configuration_cookieDomainEnabled" to enabled
        And I fill "example.com" in "synerise_integration_channel_configuration_cookieDomain"
        And I click "Events tracking" button
        And I select "product.addToCart" and "product.removeFromCart" in a "synerise_integration_channel_configuration_events-ts-control"
        And I set "synerise_integration_channel_configuration_snrsParamsEnabled" to enabled
        And I click "Configure" button
        Then I should be notified that it has been successfully configured
        And I should see "channel" channel and "workspace" workspace names in the "1" tab
        And I should see "Yes" in a "1" row at "2" tab
        And I should see "No" in a "2" row at "2" tab
        And I should see "No" in a "3" row at "2" tab
        And I should see "example.com" in a "4" row at "2" tab
        And I should see selected tracking events "product.addToCart" and "product.removeFromCart" in a "1" row at "3" tab
        And I should see "Yes" in a "2" row at "3" tab
