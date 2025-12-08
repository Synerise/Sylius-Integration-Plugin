@synchronization
Feature: Synchronization
  In order to configure synchronization with a chosen already configured synchronization configuration
  As an Administrator
  I want to configure synchronization with selected synchronization configuration

  Background:
    Given there is a workspace with test api key and request logging enabled
    And the store operates on a single channel in "United States"
    And this workspace is assigned to this channel
    And the store allows paying with "Cash on Delivery"
    And the store ships everywhere for Free
    And the store classifies its products as "Main"
    And the "Main" taxon has child taxon "Clothes"
    And the "Clothes" taxon has child taxon "Shirts"
    And the store has a textarea product attribute "Description"
    And the store has a text product attribute "Brand"
    And the store has a integer product attribute "Pages"
    And the store has a product "Jeans" with code "custom_code", created at "2025-01-10"
    And this product has a text attribute "Brand" with value "Jeans brand"
    And the store has a product "T-Shirt" priced at "$20.00"
    And this product has a main taxon "Shirts"
    And this product has a text attribute "Brand" with value "Test brand"
    And this product has a textarea attribute "Description" with value "Test description"
    And the store has customer "John Doe" with email "john.doe@mail.com"
    And the store has customer "jane.doe@mail.com" with name "Jane Doe" and phone number "123123123" since "2025-01-10"
    And I am logged in as an administrator
    When I process 2 messages

  @default
  Scenario: Successfully save customer synchronization from listing
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1     |
        | productAttributeValue | value |
    And api response will be mocked with:
        | accepted |
    And I am on "/admin/synerise/synchronization_configuration/"
    When I follow "New sync"
    Then the url should match "/admin/synerise/synchronization_configuration/\d+/synchronization/new"
    When I select "customer" from "synerise_integration_synchronization[type]"
    And I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    And I fill in "2025-02-01" for "synerise_integration_synchronization[untilWhen]"
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
    When I process 1 message with synchronization bus
    Then I should have 1 more message in the queue
    When I process 1 more message with synchronization bus
    Then I should have 0 message in the queue
    And logs should show 1 request to "/v4/clients/batch" with data:
        | body        | notContains | "email":"john.doe@mail.com"  |
        | body        | contains    | "email":"jane.doe@mail.com"  |
        | body        | contains    | "firstName":"Jane"           |
        | body        | contains    | "lastName":"Doe"             |
        | body        | contains    | "phone":"123123123"          |
        | body        | contains    | "sex":"NOT_SPECIFIED"        |
        | body        | contains    | "agreements":{"email":false} |
        | status_code | eq          | 202                          |

  @default
  Scenario: Successfully save product synchronization from synchronization configuration view
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1     |
        | productAttributes     | Brand |
        | productAttributeValue | value |
    And api response will be mocked with:
        | accepted |
    And I am on saved synchronization configuration page
    When I follow "New sync"
    And I select "product" from "synerise_integration_synchronization[type]"
    And I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    And I fill in "2025-02-01" for "synerise_integration_synchronization[untilWhen]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    And the url should match "/admin/synerise/synchronization_configuration/\d+"
    And the saved synchronization should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "1"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(1)" element should contain "Product"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(2)" element should contain "0"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(4)" element should contain current date
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(5)" element should contain "CREATED"
    And I should have 1 message in the queue
    When I process 1 message with synchronization bus
    Then I should have 1 more message in the queue
    When I process 1 more message with synchronization bus
    Then I should have 0 message in the queue
    And logs should show 1 request to "/catalogs/bags/1/items/batch" with data:
        | body        | regex       | /"id":\d+/             |
        | body        | contains    | "itemId":"custom_code" |
        | body        | contains    | "title":"Jeans"        |
        | body        | contains    | "link"                 |
        | body        | contains    | "Brand":"Jeans brand"  |
        | body        | notContains | "Description"          |
        | body        | notContains | "Pages"                |
        | body        | notContains | "title":"T-Shirt"      |
        | status_code | eq          | 202                    |

  @default
  Scenario: Successfully save product synchronization with id_value mapping
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1                 |
        | productAttributes     | Brand,Description |
        | productAttributeValue | id_value          |
    And api response will be mocked with:
        | accepted |
    And I am on saved synchronization configuration page
    When I follow "New sync"
    And I select "product" from "synerise_integration_synchronization[type]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    Then I should have 1 message in the queue
    When I process 1 message with synchronization bus
    Then I should have 1 more message in the queue
    When I process 1 more message with synchronization bus
    Then I should have 0 message in the queue
    And logs should show 1 request to "/catalogs/bags/1/items/batch" with data:
        | body        | regex       | /"id":\d+/                                            |
        | body        | contains    | "itemId":"T_SHIRT"                                    |
        | body        | contains    | "title":"T-Shirt"                                     |
        | body        | contains    | "link"                                                |
        | body        | regex       | /"Brand":{"id":\d+,"value":"Test brand"}/             |
        | body        | regex       | /"Description":{"id":\d+,"value":"Test description"}/ |
        | body        | notContains | "Pages"                                               |
        | status_code | eq          | 202                                                   |

  @default
  Scenario: Successfully save product synchronization with id mapping
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1                 |
        | productAttributes     | Pages,Description |
        | productAttributeValue | id                |
    And api response will be mocked with:
        | accepted |
    And I am on saved synchronization configuration page
    When I follow "New sync"
    And I select "product" from "synerise_integration_synchronization[type]"
    And I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    Then I should have 1 message in the queue
    When I process 1 message with synchronization bus
    Then I should have 1 more message in the queue
    When I process 1 more message with synchronization bus
    Then I should have 0 message in the queue
    And logs should show 1 request to "/catalogs/bags/1/items/batch" with data:
        | body        | regex       | /"id":\d+/             |
        | body        | contains    | "itemId":"T_SHIRT"     |
        | body        | contains    | "title":"T-Shirt"      |
        | body        | contains    | "itemId":"custom_code" |
        | body        | contains    | "title":"Jeans"        |
        | body        | contains    | "link"                 |
        | body        | regex       | /"Description":\d+/    |
        | body        | notContains | "Brand"                |
        | body        | notContains | "Pages"                |
        | status_code | eq          | 202                    |

  @default
  Scenario: Successfully save order synchronization from listing
    Given there is a already configured synchronization configuration for this channel
    And there is a "Completed" "#00000001" order with "T-shirt" product in this channel placed by customer "john.doe@mail.com" at "2024-01-15"
    And there is a "Cancelled" "#00000002" order with "T-shirt" product in this channel placed by customer "john.doe@mail.com" at "2025-01-10"
    And there is a "Completed" "#00000003" order with "T-shirt" product in this channel placed by customer "jane.doe@mail.com" at "2025-01-15"
    And there is a "Cancelled" "#00000004" order with "T-shirt" product in this channel placed by customer "jane.doe@mail.com" at "2025-02-10"
    And the synchronization configuration is configured with settings:
        | catalogId             | 1     |
        | productAttributeValue | value |
    And api response will be mocked with:
        | accepted |
    And I am on "/admin/synerise/synchronization_configuration/"
    When I follow "New sync"
    Then the url should match "/admin/synerise/synchronization_configuration/\d+/synchronization/new"
    When I select "order" from "synerise_integration_synchronization[type]"
    And I fill in "2025-01-01" for "synerise_integration_synchronization[sinceWhen]"
    And I fill in "2025-02-01" for "synerise_integration_synchronization[untilWhen]"
    And I press "Create"
    Then I should see "Synchronization has been successfully created."
    And the url should match "/admin/synerise/synchronization_configuration/\d+"
    And the saved synchronization configuration should exist in repository
    And the "#card-config tr:nth-child(1) td:last-child" element should contain "1"
    And the "#card-config tr:nth-child(2) td:last-child" element should contain catalog name
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(1)" element should contain "Order"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(2)" element should contain "0"
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(4)" element should contain current date
    And the "[data-live-name-value='synerise_integration:synchronizations_list'] tr:nth-child(1) td:nth-child(5)" element should contain "CREATED"
    And I should have 1 message in the queue
    When I process 1 message with synchronization bus
    Then I should have 1 message in the queue
    When I process 1 more message with synchronization bus
    Then I should have 0 message in the queue
    And logs should show 1 request to "/v4/transactions/batch" with data:
        | body        | notContains | 2024-01-15                                      |
        | body        | notContains | 2025-02-10                                      |
        | body        | Contains    | 2025-01-15                                      |
        | body        | Contains    | 2025-01-10                                      |
        | body        | Contains    | "name":"T-Shirt - T-Shirt"                      |
        | body        | Contains    | "sku":"T_SHIRT"                                 |
        | body        | Contains    | "category":"Main > Clothes > Shirts"            |
        | body        | Contains    | "email":"john.doe@mail.com"                     |
        | body        | Contains    | "email":"jane.doe@mail.com"                     |
        | body        | Contains    | "finalUnitPrice":{"amount":20,"currency":"USD"} |
        | body        | Contains    | "value":{"amount":20,"currency":"USD"}          |
        | status_code | eq          | 202                                             |

  @default
  Scenario: Errors on invalid form during setting synchronization
    Given there is a already configured synchronization configuration for this channel
    And the synchronization configuration is configured with settings:
        | catalogId             | 1     |
        | productAttributeValue | value |
    And I am on "/admin/synerise/synchronization_configuration/"
    When I follow "New sync"
    Then the url should match "/admin/synerise/synchronization_configuration/\d+/synchronization/new"
    When I fill in "2025-02-01" for "synerise_integration_synchronization[sinceWhen]"
    And I fill in "2025-01-01" for "synerise_integration_synchronization[untilWhen]"
    And I press "Create"
    Then I should see "This form contains errors."
    And I should see 3 "[name='synerise_integration_synchronization[type]'].is-invalid" element
    And the "#synerise_integration_synchronization_type + .invalid-feedback" element should contain "Please select an data scope"
    And I should see a "#synerise_integration_synchronization_sinceWhen.is-invalid" element
    And the "#synerise_integration_synchronization_sinceWhen + .invalid-feedback" element should contain "The \"Since when\" field must be less than or equal to the \"Until when\" field"
    And I should see a "#synerise_integration_synchronization_untilWhen.is-invalid" element
    And the "#synerise_integration_synchronization_untilWhen + .invalid-feedback" element should contain "The \"Until when\" field must be greater than or equal to the \"Since when\" field"
