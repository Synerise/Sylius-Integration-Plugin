@managing_channel_configurations
Feature: Channel Configuration Save
    In order to configure channel with a chosen workspace
    As an Administrator
    I want to configure selected channel with workspace

    Background:
        Given the store operates on a single channel in the "United States" named "channelName"
        And the store has a workspace named "workspaceName"
        And I am logged in as an administrator

    Scenario: Successfully save channel configuration
        Given I am on "/admin/synerise/configuration/new"
        When I select "channelName" from "synerise_integration_channel_configuration_channel"
        And I select "workspaceName" from "synerise_integration_channel_configuration_workspace"
        And I check "synerise_integration_channel_configuration_trackingEnabled"
        And I check "synerise_integration_channel_configuration_cookieDomainEnabled"
        And I select "product.addToCart" from "synerise_integration_channel_configuration_events-ts-control"
        And I additionally select "product.removeFromCart" from "synerise_integration_channel_configuration_events-ts-control"
        And I check "synerise_integration_channel_configuration_snrsParamsEnabled"
        And I press "Configure"
        Then the ".alert[data-test-sylius-flash-message-type='success']" element should contain "Channel configuration has been successfully created."
        And the ".page-body .card:nth-child(1)" element should contain "channelName"
        And the ".page-body .card:nth-child(1)" element should contain "workspaceName"
        And the ".page-body .card:nth-child(2) tr:nth-child(1) td:last-child" element should contain "Yes"
        And the ".page-body .card:nth-child(2) tr:nth-child(2) td:last-child" element should contain "No"
        And the ".page-body .card:nth-child(2) tr:nth-child(3) td:last-child" element should contain "No"
        And the ".page-body .card:nth-child(3) tr:nth-child(1) td:last-child" element should contain "product.addToCart"
        And the ".page-body .card:nth-child(3) tr:nth-child(1) td:last-child" element should contain "product.removeFromCart"
        And the ".page-body .card:nth-child(3) tr:nth-child(2) td:last-child" element should contain "Yes"
