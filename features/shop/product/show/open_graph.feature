@open_graph @default
Feature: OpenGraph meta tags on product page
    In order to enrich products data collected by tracker
    As a visitor
    I want to see correct OpenGraph meta tags on product pages

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Symfony T-Shirt" priced at "$19.99"
        And this product has an image "lamborghini.jpg" with "main" type at position 0
        And this product has a text attribute "code" with value "SYMFONY_T_SHIRT"

    Scenario: Viewing OpenGraph meta tags on a product page with regular price
        When the channel has OpenGraph integration enabled
        And I view product "Symfony T-Shirt"
        And I should see meta tag "og:title" with value "Symfony T-Shirt"
        And I should see meta tag "og:image"
        And I should see meta tag "product:price:amount" with value "19.99"
        And I should see meta tag "product:retailer_part_no" with value "SYMFONY_T_SHIRT"
        And I should not see meta tag "product:sale_price:amount"
        And I should not see meta tag "product:original_price:amount"

    Scenario: Viewing OpenGraph meta tags on a product page with discounted price
        When the channel has OpenGraph integration enabled
        And the product "Symfony T-Shirt" has original price "$29.99"
        And I view product "Symfony T-Shirt"
        Then I should see meta tag "og:title" with value "Symfony T-Shirt"
        And I should see meta tag "og:image"
        And I should see meta tag "product:sale_price:amount" with value "19.99"
        And I should see meta tag "product:original_price:amount" with value "29.99"

    Scenario: OpenGraph meta tags should not be present when integration is disabled
        Given the channel has OpenGraph integration disabled
        And I view product "Symfony T-Shirt"
        Then I should not see meta tag "og:title"
        And I should not see meta tag "og:image"
        And I should not see meta tag "product:price:amount"
        And I should not see meta tag "product:sale_price:amount"
        And I should not see meta tag "product:original_price:amount"
