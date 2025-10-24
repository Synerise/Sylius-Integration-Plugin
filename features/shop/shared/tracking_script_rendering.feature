@tracking_script @default
Feature: Tracking Script Rendering
  In order to ensure proper analytics tracking
  As a website visitor
  I want the tracking script to be rendered correctly when enabled

  Background:
    Given the store operates on a single channel in "United States"
    And I have a configured channel with workspace

  Scenario: Tracking script renders with minimal configuration
    Given the channel has tracking enabled with tracking code "test-tracker-key"
    When I visit a page
    Then I should see the tracking script in the page source
    And the script should contain the tracker host "web.snrbox.com"
    And the script should initialize with correct options:
          | trackerKey      | test-tracker-key   |

  Scenario: Tracking script is not rendered when enabled without tracking code
    Given the channel has tracking enabled without tracking code
    And the channel is configured with settings:
          | cookieDomain    | example.com       |
    When I visit a page
    Then I should not see the tracking script in the page source

  Scenario: Tracking script is not rendered when disabled
    Given the channel has tracking disabled with tracking code "test-tracker-key"
    When I visit a page
    Then I should not see the tracking script in the page source


  Scenario: Tracking script is rendered when enabled with all required data
    Given the channel has tracking enabled with tracking code "test-tracker-key"
    And the channel is configured with settings:
        | cookieDomain      | example.com       |
        | customPageVisit   | true              |
        | virtualPage       | true              |
    When I visit a page
    Then I should see the tracking script in the page source
    And the script should initialize with correct options:
      | trackerKey          | test-tracker-key     |
      | domain              | .example.com         |
      | customPageVisit     | true                 |
      | dynamicContent      | {"virtualPage":true} |
