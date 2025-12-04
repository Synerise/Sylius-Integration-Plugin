@client_register @event
Feature: Customer registration event processing
  In order to integrate with Synerise
  As a system administrator
  I want customer registration events to be processed based on configuration

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel

  @default @no-queue
  Scenario: Registering a new account without uuid cookie
    Given on this channel account verification is required
    And the channel is configured with settings:
        | events    | client.register   |
    And api response will be mocked with:
        | accepted  |
    When I want to register a new account
    And I specify the first name as "Saul"
    And I specify the last name as "Goodman"
    And I specify the email as "goodman@example.com"
    And I specify the password as "heisenberg"
    And I confirm this password
    And I register this account
    Then I should be notified that new account has been successfully created
    And I should be on registration thank you page
    And logs should show 1 request to "/v4/events/registered" with data:
        | body        | contains | "email":"goodman@example.com" |
        | status_code | eq       | 202                           |
    And logs should show 1 request in total

  @default @identify @no-queue
  Scenario: Registering a new account with uuid cookie
    Given on this channel account verification is required
    And the channel is configured with settings:
        | events    | client.register   |
    And api response will be mocked with:
        | accepted  |
    And tracking cookies are set
        | _snrs_uuid    | 4b561e92-d398-45d8-b581-a6988627277a                                                                  |
        | _snrs_p       | permUuid:4b561e92-d398-45d8-b581-a6988627277a&uuid:4b561e92-d398-45d8-b581-a6988627277a&identityHash: |
    When I want to register a new account
    And I specify the first name as "Saul"
    And I specify the last name as "Goodman"
    And I specify the email as "goodman@example.com"
    And I specify the password as "heisenberg"
    And I confirm this password
    And I register this account
    Then I should be notified that new account has been successfully created
    And I should be on registration thank you page
    And logs should show 1 request to "/v4/clients/batch" with data:
        | body        | contains | {"email":"goodman@example.com","uuid":"4b561e92-d398-45d8-b581-a6988627277a"},{"email":"goodman@example.com","uuid":"0083edaf-d956-524f-a131-fc32fbae99fd"} |
        | status_code | eq       | 202                                                                                                                                                         |
    And logs should show 1 request to "/v4/events/registered" with data:
        | body        | contains | "email":"goodman@example.com","uuid":"0083edaf-d956-524f-a131-fc32fbae99fd"   |
        | status_code | eq       | 202                                                                           |
    And logs should show 2 requests in total

  @default @queue
  Scenario: Registering a new account with queue
    Given on this channel account verification is required
    And the channel is configured with settings:
        | events        | client.register   |
        | queue_events  | client.register   |
    And api response will be mocked with:
        | accepted  |
    When I want to register a new account
    And I specify the first name as "Saul"
    And I specify the last name as "Goodman"
    And I specify the email as "goodman@example.com"
    And I specify the password as "heisenberg"
    And I confirm this password
    And I register this account
    Then I should be notified that new account has been successfully created
    And I should be on registration thank you page
    And I should have 1 message in the queue
    When I process 1 message
    Then logs should show 1 request to "/v4/events/registered" with data:
        | body        | contains | "email":"goodman@example.com" |
        | status_code | eq       | 202                           |
    And logs should show 1 requests in total

  @e2e @javascript @no-queue
  Scenario: Registering a new account
    Given on this channel account verification is required
    And the channel is configured with settings:
        | events    | client.register   |
    And the channel has tracking enabled with test tracking code
    When I want to register a new account
    And the actual api requests will be sent
    And I specify the first name as "Saul"
    And I specify the last name as "Goodman"
    And I specify the email as "goodman@example.com"
    And I specify the password as "heisenberg"
    And I confirm this password
    And I register this account
    Then I should be notified that new account has been successfully created
    And I should be on registration thank you page
    And logs should show 1 request to "/v4/clients/batch" with data:
        | body        | contains | {"email":"goodman@example.com","uuid":"0083edaf-d956-524f-a131-fc32fbae99fd"} |
        | status_code | eq       | 202                                                                           |
    And logs should show 1 request to "/v4/events/registered" with data:
        | body        | contains | "email":"goodman@example.com","uuid":"0083edaf-d956-524f-a131-fc32fbae99fd"   |
        | status_code | eq       | 202                                                                           |
    And logs should show 2 requests in total

