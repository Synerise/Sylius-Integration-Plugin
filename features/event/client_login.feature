@client_login @event
Feature: Customer login event processing
  In order to integrate with Synerise
  As a system administrator
  I want customer login events to be processed based on configuration

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel
    And the store has customer "John Doe" with email "car@better.com"
    And I have already registered "car@better.com" account

  @default @no-queue
  Scenario: Logging in to an account without uuid cookie
    Given the channel is configured with settings:
        | events    | client.login   |
    And api response will be mocked with:
        | accepted  |
    When I log in with the email "car@better.com"
    Then I should be logged in
    And logs should show 1 request to "/v4/events/logged-in" with data:
        | body        | contains | "email":"car@better.com" |
        | status_code | eq       | 202                      |
    And logs should show 1 request in total

  @default @identify @no-queue
  Scenario: Logging in to an account with uuid cookie
    Given the channel is configured with settings:
        | events    | client.login   |
    And api response will be mocked with:
        | accepted  |
    And tracking cookies are set
        | _snrs_uuid    | 4b561e92-d398-45d8-b581-a6988627277a                                                                  |
        | _snrs_p       | permUuid:4b561e92-d398-45d8-b581-a6988627277a&uuid:4b561e92-d398-45d8-b581-a6988627277a&identityHash: |
    When I log in with the email "car@better.com"
    Then I should be logged in
    And logs should show 1 request to "/v4/clients/batch" with data:
        | body        | contains | [{"email":"car@better.com","uuid":"4b561e92-d398-45d8-b581-a6988627277a"},{"email":"car@better.com","uuid":"77cb3081-542b-5cf3-9938-26be8dcd27f6"}]  |
        | status_code | eq       | 202                                                                                                                                                  |
    And logs should show 1 request to "/v4/events/logged-in" with data:
        | body        | contains | "email":"car@better.com","uuid":"77cb3081-542b-5cf3-9938-26be8dcd27f6" |
        | status_code | eq       | 202                                                                    |
    And logs should show 2 requests in total

  @default @queue
  Scenario: Logging in to an account without uuid cookie with queue enabled
    Given the channel is configured with settings:
        | events        | client.login   |
        | queue_events  | client.login   |
    And api response will be mocked with:
        | accepted  |
    When I log in with the email "car@better.com"
    Then I should be logged in
    And I should have 1 message in the queue
    When I process 1 message
    Then logs should show 1 request to "/v4/events/logged-in" with data:
        | body        | contains | "email":"car@better.com" |
        | status_code | eq       | 202                      |
    And logs should show 1 request in total

  @e2e @javascript @identify @no-queue
  Scenario: Logging in to an account with real tracking and api
    Given the channel is configured with settings:
        | events    | client.login   |
    And the channel has tracking enabled with test tracking code
    And I want to log in
    And the actual api requests will be sent
    When I log in with the email "car@better.com"
    Then I should be logged in
    And logs should show 1 request to "/v4/clients/batch" with data:
        | body          | "email":"car@better.com","uuid":"77cb3081-542b-5cf3-9938-26be8dcd27f6"    |
        | status_code   | 202                                                                       |
    And logs should show 1 request to "/v4/events/logged-in" with data:
        | body        | contains | "email":"car@better.com","uuid":"77cb3081-542b-5cf3-9938-26be8dcd27f6"    |
        | status_code | eq       | 202                                                                       |
    And logs should show 2 requests in total
