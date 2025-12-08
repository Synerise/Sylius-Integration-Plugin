@client_logout @event
Feature: Customer logout event processing
  In order to integrate with Synerise
  As a system administrator
  I want customer logout events to be processed based on configuration

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel
    And I am a logged in customer
    And I am browsing my orders

  @default @no-queue
  Scenario: Logging out of an account
    Given the channel is configured with settings:
        | events    | client.logout   |
    And api response will be mocked with:
        | accepted  |
    When I log out
    Then I should be redirected to the homepage
    And I should not be logged in
    And logs should show 1 request to "/v4/events/logged-out" with data:
        | body        | contains | "email":"shop@example.com" |
        | status_code | eq       | 202                        |
    And logs should show 1 request in total

  @default @queue
  Scenario: Logging out of an account with queue enabled
    Given the channel is configured with settings:
        | events        | client.logout   |
        | queue_events  | client.logout   |
    And api response will be mocked with:
        | accepted  |
    When I log out
    Then I should be redirected to the homepage
    And I should not be logged in
    And I should have 1 message in the queue
    When I process 1 message with event bus
    Then logs should show 1 request to "/v4/events/logged-out" with data:
        | body        | contains | "email":"shop@example.com" |
        | status_code | eq       | 202                        |
    And logs should show 1 request in total

  @e2e @no-queue
  Scenario: Logging out of an account with real tracking and api
    Given the channel is configured with settings:
        | events    | client.logout   |
    And the channel has tracking enabled with test tracking code
    And the actual api request will be sent
    When I log out
    Then I should be redirected to the homepage
    And I should not be logged in
    And logs should show 1 request to "/v4/events/logged-out" with data:
        | body        | contains | "email":"shop@example.com" |
        | status_code | eq       | 202                        |
    And logs should show 1 request in total
