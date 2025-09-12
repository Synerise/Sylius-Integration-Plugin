@managing_channel_configurations
Feature: Channel Configuration Save
    In order to configure channel with a chosen workspace
    As an Administrator
    I want to configure selected channel with workspace

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Save channel configuration
        When I want to create channel configuration
        And I select channel
        And I select workspace
        And I click "Next" button
        And I set "Automatically add tracking code to monitor customer activity" to enabled
        And I click "Events tracking" button from side nav
        And I select "product.addToCart" and "product.removeFromCart" in a Tracking events
        And I set "Add tracking parameters to cart and transaction events" to enabled
        And I click "Configure" button
        Then I should be notified that it has been successfully configured
        And I should see channel and workspace names in the first tab
        And I should see "Yes" in a "Automatically add tracking code to monitor customer" row at second tab
        And I should see "No" in a "Render OG tags from page visit events" row at second tab
        And I should see "No" in a "Dynamic content for PWA, SPA" row at second tab
        And I should see domain name in a "Override cookie domain" row at second tab
        And I should see selected tracking events "product.addToCart" and "product.removeFromCart" in a "Tracking events" row at third tab
        And I should see "Yes" in a "Add tracking parameters to cart and transaction events" row at third tab
