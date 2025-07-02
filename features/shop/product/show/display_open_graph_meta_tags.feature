@opengraph @channel_configuration
Feature: OpenGraph meta tags on product page
    In order to share products on social media
    As a visitor
    I want to see correct OpenGraph meta tags on product pages

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Symfony T-Shirt" priced at "$19.99"
        And this product has an image "lamborghini.jpg" with "main" type at position 0
        And the channel has OpenGraph integration enabled

    Scenario: Viewing OpenGraph meta tags on a product page with regular price
        When I view product "Symfony T-Shirt"
        Then I should see OpenGraph meta tag "title" with value "Symfony T-Shirt"
        And I should see OpenGraph meta tag "image" with value "*/lamborghini.jpg"
        And I should see OpenGraph meta tag "price" with value "$19.99"
        And I should see OpenGraph meta tag "retailer_part_no" with value "*"
        And I should not see OpenGraph meta tag "sale_price"
        And I should not see OpenGraph meta tag "original_price"

    Scenario: Viewing OpenGraph meta tags on a product page with discounted price
        Given the product "Symfony T-Shirt" has original price "$29.99"
        And the product "Symfony T-Shirt" has price "$19.99"
        When I view product "Symfony T-Shirt"
        Then I should see OpenGraph meta tag "title" with value "Symfony T-Shirt"
        And I should see OpenGraph meta tag "image" with value "*/lamborghini.jpg"
        And I should see OpenGraph meta tag "sale_price" with value "$19.99"
        And I should see OpenGraph meta tag "original_price" with value "$29.99"

    Scenario: OpenGraph meta tags should not be present when integration is disabled
        Given the channel has OpenGraph integration disabled
        When I view product "Symfony T-Shirt"
        Then I should not see OpenGraph meta tag "title"
        And I should not see OpenGraph meta tag "image"
        And I should not see OpenGraph meta tag "price"
        And I should not see OpenGraph meta tag "sale_price"
        And I should not see OpenGraph meta tag "original_price"
